<?php

namespace dxkite\support\visitor\response;

use Exception;
use ReflectionClass;
use JsonSerializable;
use ReflectionMethod;
use suda\tool\Command;
use ReflectionFunction;
use suda\template\Template;

use dxkite\support\file\File;
use dxkite\support\visitor\Context;
use dxkite\support\visitor\Permission;
use dxkite\support\visitor\response\ResponseObject;
use dxkite\support\visitor\exception\MethodCallExeption;

/**
 * 自动调用函数接口来响应
 */
abstract class MethodCallResponse extends Response
{
    // 参数生成
    const PARAM_GET= 'GET';
    const PARAM_POST= 'POST';
    const PARAM_JSON=  'JSON';
    const PARAM_FILES= 'FILES';

    protected $export;
    protected $request;

    // 默认调用接口
    protected $defaultMethod='__default';
    // 默认函数调用参数来源
    protected $defaultParams=[MethodCallResponse::PARAM_GET];
    // 默认接口开放状态
    protected $defaultOpen=true;

    public function onVisit(Context $context)
    {
        $this->export=$this->getExportMethods();
        $request=$context->getRequest();
        $this->request=$request;
        if ($request->hasHeader('XRPC-Method')) {
            $method = trim($request->getHeader('XRPC-Method'));
        } else {
            $method = $request->get('method', $this->defaultMethod);
        }
        $paramSourceFrom= $this->getParameterFrom($this->export[$method]['comment'] ?? '');
        if (array_key_exists($method, $this->export)) {
            $callback = $this->export[$method]['callback'];
        } else {
            $callback = [$this, $this->defaultMethod];
        }
        $paramSource = $this->getSourceParams($paramSourceFrom);
        $object = $this->buildObject($callback);
        $method = $this->buildMethod($callback);
        $params = $this->buildMethodParams($method, $paramSource);
        $responseObject = $this->runMethod($object, $method, $params);
        return $this->buildResponse($responseObject);
    }
    
    public function buildResponse($responseObject)
    {
        if ($responseObject instanceof ResponseObject) {
            $responseObject->makeResponse($this);
        } elseif (in_array(\gettype($responseObject), ['boolean','bool','integer','int','float','string','array', 'null']) || $responseObject instanceof JsonSerializable) {
            $this->json($responseObject);
        } else {
            return $responseObject;
        }
    }

    protected function buildObject($callback)
    {
        if (\is_array($callback) && count($callback) === 2 && \is_object($callback[0])) {
            return $callback[0];
        } elseif (is_null($onject)) {
            if ($method->getDeclaringClass()===get_class($this)) {
                return $this;
            } else {
                return $method->getDeclaringClass()->newInstance();
            }
        } else {
            return null;
        }
    }

    protected function buildMethod($method)
    {
        if ($method instanceof \ReflectionMethod || $method instanceof \ReflectionFunction) {
        } elseif (is_array($method) && count($method)>1) {
            $method=new ReflectionMethod($method[0], $method[1]);
        } else {
            $method=new ReflectionFunction($method);
        }
        return $method;
    }

    /**
     * 检测对象是否可以为某对象
     *
     * @param object $object
     * @param string $type
     * @return boolean
     */
    protected function objectCanBeType($object, string $type):bool
    {
        $class = new ReflectionClass($object);
        return $class->isSubclassOf($type) || $class->implementsInterface($type);
    }

    
    protected function newObjectWithData(string $object, string $name, $data):?object
    {
        $class = new ReflectionClass($object);
        if ($class->implementsInterface(MethodParameter::class)) {
            if (request()->isJson()) {
                return $class->getMethod('createFromJson')->invoke(null, $data);
            } else {
                return $class->getMethod('createFromPost')->invoke(null, $name, $data);
            }
        } elseif (\is_array($data) && $class->isInstantiable()) {
            $instance = $class ->newInstance();
            foreach ($array as $name => $value) {
                if ($property = $class->hasProperty($name)) {
                    $methodName = 'set'.\ucfirst($name);
                    if ($class->hasMethod($methodName)) {
                        $class->getMethod($methodName)->invoke($instance, $value);
                    } elseif ($property->isPublic()) {
                        $property->setValue($instance, $value);
                    }
                }
            }
            return $instance;
        }
        return null;
    }

    protected function buildMethodParams($method, array $params)
    {
        $args=[];
        // 压入调用参数
        foreach ($method->getParameters() as $param) {
            $name=$param->getName();
            $pos=$param->getPosition();
            if (\array_key_exists($name, $params)) {
                if ($param->hasType()) {
                    $val=$params[$name];
                    $typeName=is_object($val)?get_class($val):gettype($val);
                    try {
                        $paramTypeName=$param->getType()->__toString();
                        if ($paramTypeName === $typeName) {
                            $args[$pos]=$val;
                        } elseif (\is_object($params[$name]) && self::objectCanBeType($object, $paramTypeName)) {
                            $args[$pos]=$val;
                        } elseif (in_array($paramTypeName, ['boolean','bool','integer','int','float','string','array','object','null']) && settype($val, $paramTypeName)) {
                            $args[$pos]=$val;
                        } elseif ($obj=self::newObjectWithData($paramTypeName, $name, $val)) {
                            $args[$pos]=$obj;
                        } else {
                            throw (new MethodCallExeption(__('parameter convent error: parameter $0 from $1 to $2', $name, $typeName, $paramTypeName), -32602))->setName('InvalidParams');
                        }
                    } catch (MethodCallExeption $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        throw (new MethodCallExeption(__('parameter convent error: parameter $0 from $1 to $2', $name, $typeName, $paramTypeName), -32602))->setName('InvalidParams');
                    }
                } else {
                    $args[$pos]=$params[$name];
                }
            } elseif ($param->isDefaultValueAvailable()) {
                $args[$pos] = $param->getDefaultValue();
            } elseif ($param->allowsNull()) {
                $args[$pos] = null;
            } else {
                throw (new MethodCallExeption(__('require parameter $0', $name), -32602))->setName('InvalidParams')->setData(['name'=>$name, 'pos'=>$pos]);
            }
        }
        return $args;
    }

    abstract public function __default();
    
    /**
     * 获取导出的接口
     *
     * @return void
     */
    public function getExportMethods($enter=null)
    {
        $enterclass=is_null($enter)?$this:$enter;
        $methods=[];
        if (is_array($enterclass)) {
            foreach ($enterclass as $class) {
                if (is_object($class)) {
                    $classInstance=$class;
                } else {
                    $classInstance=Command::newClassInstance($class);
                }
                $methods=$this->getExportMethodFromClass($classInstance, $methods);
            }
        } else {
            if (is_object($enterclass)) {
                $classInstance=$enterclass;
            } else {
                $classInstance=Command::newClassInstance($enterclass);
            }
            $methods=$this->getExportMethodFromClass($classInstance);
        }
        return $methods;
    }

    protected function getExportMethodFromClass($enterclass, array $methods=[])
    {
        $class = new ReflectionClass($enterclass);
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name=$method->getShortName();
            if (array_key_exists($name, $methods)) {
                $callbackName = $class->getShortName() . '.' . $name;
            } else {
                $callbackName=$name;
            }
            if ($exportClasses=$class->getConstant('EXPORTS')) {
            } else {
                $exportClasses=[get_class($enterclass)];
            }
            if (in_array($method->getDeclaringClass()->getName(), $exportClasses) && !preg_match('/^__/', $name)) {
                // 如果有DOCS
                if ($docs=$method->getDocComment()) {
                    // 设置了是否开放
                    if (preg_match('/@open\s+(\w+)\s+/ims', $docs, $match)) {
                        // 是否开放端口
                        if (!filter_var(strtolower($match[1]??'true'), FILTER_VALIDATE_BOOLEAN)) {
                            continue; // 不开放则跳过
                        }
                    } elseif (!$this->defaultOpen) {
                        continue; // 不设置则跳过
                    }
                   
                    $methods[$callbackName]['comment']=$docs;
                } elseif (!$this->defaultOpen) {
                    continue; // 不设置则跳过
                }
                $methods[$callbackName]['callback']=[$enterclass,$name];
            }
        }
        return $methods;
    }

    public function runMethod($object, $method, array $paramArray)
    {
        if ($method->getDocComment()) {
            if (!$this->canAccess($method)) {
                return $this->onDeny($this->getContext());
            }
        }
        if ($method instanceof ReflectionMethod) {
            return $method->invokeArgs($object, $paramArray);
        } else {
            return $method->invokeArgs($paramArray);
        }
    }

    public function getSourceParams(array $types):array
    {
        $paramSource = [];
        foreach ($types as  $value) {
            $paramSource = array_merge($paramSource,$this->getSourceParam($value));
        }
        return $paramSource;
    }

    public function getSourceParam(string $type=MethodCallResponse::PARAM_GET):array
    {
        switch ($type) {
            case MethodCallResponse::PARAM_GET:
                return $this->request->get();
            case MethodCallResponse::PARAM_POST:
                return array_merge($this->request->post(), $this->request->files());
            case MethodCallResponse::PARAM_JSON:
                return $this->request->json() ?? [];
            case MethodCallResponse::PARAM_FILES:
                return $this->request->files();
            default:
                return array_merge($this->request->post(), $this->request->files());
        }
    }

    public function getParameterFrom(string $docs)
    {
        if (preg_match('/@param(?:-s|S)ource\s+([\w,]+)\s*$/ims', $docs, $match)) {
            return explode(',', strtoupper(trim($match[1], ',')));;
        }
        return $this->defaultParams ?? [];
    }

    public  static function canAccess($method):bool {
        if ($permission=Permission::createFromFunction($method)) {
            if (visitor()->isGuest()) {
                return false;
            }
            return visitor()->hasPermission($permission);
        }
        return true;
    }
}
