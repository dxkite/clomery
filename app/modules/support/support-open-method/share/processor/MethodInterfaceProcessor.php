<?php

namespace support\openmethod\processor;

use suda\application\template\RawTemplate;
use suda\framework\Config;
use suda\framework\debug\DebugObject;
use Throwable;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use suda\framework\Route;
use BadMethodCallException;
use suda\framework\Request;
use suda\application\Module;
use suda\framework\Response;
use InvalidArgumentException;
use suda\application\Application;
use support\openmethod\Permission;
use support\openmethod\ExportMethod;
use support\openmethod\ExportMessage;
use support\openmethod\MethodParameterBag;
use support\openmethod\AuthorizationInterface;
use suda\application\processor\RequestProcessor;
use support\openmethod\processor\ResultProcessor;
use support\openmethod\exception\PermissionException;
use support\openmethod\FrameworkContextAwareInterface;

class MethodInterfaceProcessor
{

    /**
     * 导出的类
     *
     * @var array
     */
    protected $exportClasses;

    /**
     * 响应请求
     *
     * @var Response
     */
    protected $response;

    /**
     * 应用
     *
     * @var Application
     */
    protected $application;

    /**
     * 请求处理
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return array|null
     */
    final public function onRequest(Application $application, Request $request, Response $response)
    {
        $this->response = $response;
        $this->application = $application;

        set_exception_handler([$this, 'uncaughtException']);
        $module = $request->getAttribute('module');

        if ($request->getMethod() === 'OPTIONS') {
            $this->buildAccessControlOptions($application, $request, $response, $module);
            return null;
        }

        $this->buildAccessControlAny($application, $request, $response, $module);
        $this->exportClasses = $this->getExportClasses($request->getAttribute('open-method'));
        $method = $request->getHeader('x-method', $request->get('_method'));
        $id = $request->getHeader('x-method-id', $request->get('_method_id'));

        $application->debug()->time('build getExportMethods');
        $methods = $this->getExportMethods();
        $application->debug()->timeEnd('build getExportMethods');

        if ($request->getMethod() === 'GET' && $method === null) {
            return $this->buildInterfaceMessages($methods);
        }

        try {
            $application->debug()->time('build parameter');
            /** @var MethodParameterBag $parameterBag */
            list($id, $parameterBag) = $this->buildMethodParameterBag($methods, $id, $method, $application, $request);
            $application->debug()->timeEnd('build parameter');
            $result = $this->invokeMethod($parameterBag, $application, $request, $response);
            if ($result instanceof ResultProcessor) {
                return $result->processor($application, $request, $response);
            } elseif ($result instanceof RawTemplate) {
                (new TemplateResultProcessor($result))->processor($application, $request, $response);
                return null;
            } elseif ($request->isJson() || $method !== null) {
                return [
                    'id' => $id,
                    'result' => $result,
                ];
            } else {
                return $result;
            }
        } catch (Throwable $e) {
            return $this->getExceptionJson($e, $response);
        }
    }

    protected function buildAccessControlOptions(Application $application, Request $request, Response $response, string $module)
    {
        $config = $this->getMethodConfig($application, $module);
        if (is_array($config) && array_key_exists('access-control', $config)) {
            $header = $config['access-control']['allow-header'] ?? [];
            if (count($header) > 0) {
                $response->setHeader('access-control-allow-headers', implode(',', $header));
            }
            $maxAge = $config['access-control']['max-age'] ?? 0;
            if ($maxAge > 0) {
                $response->setHeader('access-control-max-age', $maxAge);
            }
            $method = $config['access-control']['allow-method'] ?? [];
            if (count($method) > 0) {
                $response->setHeader('access-control-allow-methods', implode(',', $method));
            }
            $origin = $request->getHeader('origin', null);
            $allow = $config['access-control']['allow-origin'] ?? '*';
            $allowCookie = $config['access-control']['allow-credentials'] ?? false;
            if ($allow === 'any') {
                $response->setHeader('access-control-allow-origin', $origin === null ? '*' : $origin);
            }
            if ($allow === '*') {
                $response->setHeader('access-control-allow-origin', '*');
            }
            if (is_array($allow)) {
                $response->setHeader('access-control-allow-origin', implode(',', $allow));
            }
            if ($allowCookie) {
                $response->setHeader('access-control-allow-credentials', 'true');
            }
        }
    }

    protected function buildAccessControlAny(Application $application, Request $request, Response $response, string $module)
    {
        $config = $this->getMethodConfig($application, $module);
        if (is_array($config) && array_key_exists('access-control', $config)) {
            $origin = $request->getHeader('origin', null);
            $allow = $config['access-control']['allow-origin'] ?? '*';
            $allowCookie = $config['access-control']['allow-credentials'] ?? false;
            if ($allow === 'any') {
                $response->setHeader('access-control-allow-origin', $origin === null ? '*' : $origin);
            }
            if ($allow === '*') {
                $response->setHeader('access-control-allow-origin', '*');
            }
            if (is_array($allow)) {
                $response->setHeader('access-control-allow-origin', implode(',', $allow));
            }
            if ($allowCookie) {
                $response->setHeader('access-control-allow-credentials', 'true');
            }
        }
    }

    protected function getMethodConfig(Application $application, string $module)
    {
        $moduleObject = $application->get($module);
        $path = $moduleObject->getResource()->getConfigResourcePath('config/open-method-config');
        if ($path !== null) {
            $config = Config::loadConfig($path, [
                'module' => $moduleObject->getFullName(),
                'config' => $moduleObject->getConfig(),
            ]);
            if ($config !== null) {
                return $config;
            }
        }
        return null;
    }

    public function uncaughtException(Throwable $throwable)
    {
        $this->application->debug()->addIgnorePath(__FILE__);
        $this->application->debug()->uncaughtException($throwable);
        $this->application->dumpException($throwable);
        if ($this->response->isSend() === false) {
            $this->response->sendContent($this->getExceptionJson($throwable, $this->response));
            $this->response->end();
        }
    }

    protected function getExceptionJson(Throwable $e, Response $response): array
    {
        if ($e instanceof BadMethodCallException || $e instanceof InvalidArgumentException) {
            $response->status(400);
        }
        $this->application->debug()->uncaughtException($e);
        if (SUDA_DEBUG) {
            $this->application->dumpException($e);
            return [
                'id' => null,
                'error' => [
                    'name' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'debug-object' => new DebugObject([
                        'throwable' => $e,
                        'backtrace' => $e->getTrace(),
                    ]),
                ],
            ];
        }
        return [
            'id' => null,
            'error' => [
                'name' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ];
    }

    /**
     * @param MethodParameterBag $parameterBag
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeMethod(MethodParameterBag $parameterBag, Application $application, Request $request, Response $response)
    {
        $method = $parameterBag->getMethod();
        $parameter = $parameterBag->getParameter();
        $object = $method->getClass();
        $methodIsStatic = $method->getReflectionMethod()->isStatic();
        if (is_string($object) && $methodIsStatic === false) {
            $object = $method->getReflectionClass()->newInstance();
        }
        if ($methodIsStatic) {
            $this->assertCanAccessMethod(null, $method);
            $this->contextAware(null, $method, $application, $request, $response);
            return $method->getReflectionMethod()->invokeArgs(null, $parameter);
        } else {
            $this->assertCanAccessMethod($object, $method);
            $this->contextAware($object, $method, $application, $request, $response);
            return $method->getReflectionMethod()->invokeArgs($object, $parameter);
        }
    }

    /**
     * @param $object
     * @param ExportMethod $export
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @throws \ReflectionException
     */
    protected function contextAware($object, ExportMethod $export, Application $application, Request $request, Response $response)
    {
        $hasMethod = $export->getReflectionClass()->implementsInterface(FrameworkContextAwareInterface::class);
        if ($hasMethod) {
            $setContext = $export->getReflectionClass()->getMethod('setContext');
            if ($setContext->isStatic()) {
                $setContext->invokeArgs($object, [$application, $request, $response]);
            } else {
                $setContext->invokeArgs($object, [$application, $request, $response]);
            }
        }
    }

    /**
     * @param $object
     * @param ExportMethod $export
     * @throws \ReflectionException
     */
    protected function assertCanAccessMethod($object, ExportMethod $export)
    {
        $permission = Permission::createFromFunction($export->getReflectionMethod());
        if ($permission !== null) {
            $hasMethod = $export->getReflectionClass()->implementsInterface(AuthorizationInterface::class) || $export->getReflectionClass()->hasMethod('getPermission');
            if ($hasMethod) {
                $getPermission = $export->getReflectionClass()->getMethod('getPermission');
                if ($getPermission->isStatic()) {
                    $currentPermission = $getPermission->invoke($object);
                } else {
                    $currentPermission = $getPermission->invoke($object);
                }
            } else {
                $currentPermission = new Permission;
            }
            if ($currentPermission->surpass($permission) === false) {
                throw new PermissionException($permission, -32001);
            }
        }
    }

    protected function buildMethodParameterBag(array $methods, ?int $id, ?string $method, Application $application, Request $request)
    {
        $json = $this->getJsonFromRequest($request);
        $parameterBag = null;
        if ($method === null && $json !== null) {
            if ($this->isValidJsonMethodInvoke($json)) {
                $method = $json['method'];
                $id = $json['id'];
                $parameter = $json['params'] ?? [];
                if (!array_key_exists($method, $methods)) {
                    throw new BadMethodCallException('call undefined method ' . $method, -32601);
                }
                return [$id, new MethodParameterBag($application, $request, $methods[$method], $parameter)];
            } else {
                throw new BadMethodCallException('need method and id parameter in method call', -32600);
            }
        }
        if (!array_key_exists($method, $methods)) {
            throw new BadMethodCallException('call undefined method ' . $method);
        }
        return [$id, new MethodParameterBag($application, $request, $methods[$method], $json)];
    }

    protected function getJsonFromRequest(Request $request): ?array
    {
        if ($request->getMethod() !== 'OPTIONS' && $request->isJson()) {
            $json = json_decode($request->input(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new BadMethodCallException('create argument from json error:' . json_last_error_msg(), -32700);
            }
            return $json;
        }
        return null;
    }

    protected function isValidJsonMethodInvoke(array $json): bool
    {
        return array_key_exists('method', $json) && array_key_exists('id', $json);
    }

    /**
     * 获取导出参数
     *
     * @param ExportMethod[] $methods
     * @return array
     */
    protected function buildInterfaceMessages(array $methods): array
    {
        $messages = [];
        foreach ($methods as $key => $method) {
            $messages[$key] = new ExportMessage($method);
        }
        return $messages;
    }

    protected function getExportClasses($classes): array
    {
        if (\is_array($classes) && count($classes) > 0) {
            return $classes;
        } elseif (is_string($classes)) {
            return [$classes];
        }
        return [$this];
    }

    /**
     * 获取导出的接口
     *
     * @return ExportMethod[]
     */
    public function getExportMethods()
    {
        $methods = [];
        foreach ($this->exportClasses as $class) {
            if (is_string($class)) {
                $class = trim(str_replace('.', '\\', $class), '\\');
            }
            $methods = $this->getExportMethodFromClass($class, $methods);
        }
        return $methods;
    }

    /**
     * ExportMethod
     *
     * @param ExportMethod[] $classObject
     * @param array $methods
     * @return ExportMethod[]
     */
    protected function getExportMethodFromClass($classObject, array $methods = [])
    {
        $class = new ReflectionClass($classObject);
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getShortName();
            // 多类命名冲突
            if (array_key_exists($name, $methods)) {
                $callbackName = $class->getShortName() . '.' . $name;
            } else {
                $callbackName = $name;
            }
            if ($exportClasses = $class->getConstant('EXPORTS')) {
            } else {
                $exportClasses = is_object($classObject) ? [get_class($classObject)] : [$classObject];
            }
            if (in_array($method->getDeclaringClass()->getName(), $exportClasses) && !preg_match('/^__/', $name)) {
                // 如果有DOCS
                if ($docs = $method->getDocComment()) {
                    // 设置了是否开放
                    if (preg_match('/@open\s+(\w+)\s+/ims', $docs, $match)) {
                        // 是否开放端口
                        if (!filter_var(strtolower($match[1] ?? 'true'), FILTER_VALIDATE_BOOLEAN)) {
                            continue; // 不开放则跳过
                        }
                    }
                }
                $methods[$callbackName] = new ExportMethod($classObject, $name, $class, $method);
            }
        }
        return $methods;
    }
}
