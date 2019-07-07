<?php

namespace support\openmethod;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use suda\framework\Request;
use InvalidArgumentException;
use suda\application\Application;
use suda\framework\http\UploadedFile;
use suda\framework\response\ContentWrapper;

class MethodParameterBag
{

    /**
     * 方法引用
     *
     * @var ExportMethod
     */
    protected $method;

    /**
     * 类引用
     *
     * @var Application
     */
    protected $applcation;

    /**
     * 响应
     *
     * @var Request
     */
    protected $request;

    /**
     * 是否含JSON数据
     *
     * @var mixed
     */
    protected $json;

    /**
     * 数据源
     *
     * @var array
     */
    protected $sourceFrom;

    public function __construct(Application $app, Request $request, ExportMethod $method, $json = null)
    {
        $this->applcation = $app;
        $this->request = $request;
        $this->method = $method;
        $this->json = $json;
        $this->sourceFrom = $this->getParameterFrom($method->getReflectionMethod()->getDocComment());
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getParameter(): array
    {
        $method = $this->method->getReflectionMethod();
        $parameter = [];
        // 压入调用参数
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $position = $param->getPosition();
            $value = $this->buildTypeValue($position, $name, $param);
            if ($param->hasType()) {
                if ($value !== null && ContentWrapper::isTypeOf($value, $param->getType()) === false) {
                    throw new InvalidArgumentException('parameter ' . $name . ' at ' . $position . ' must be ' . $param->getType(), -32602);
                }
            }
            $parameter[$position] = $value;
        }
        return $parameter;
    }

    public function getParameterFrom(string $docs)
    {
        if (preg_match('/@param-source\s+([\w,]+)\s*$/ims', $docs, $match)) {
            return explode(',', strtoupper(trim($match[1], ',')));
        }
        return $this->json !== null ? ['JSON'] : ['POST', 'GET'];
    }

    public function isPHPInnerType(string $typeName): bool
    {
        return in_array($typeName, ['boolean', 'bool', 'integer', 'int', 'float', 'string', 'array']);
    }

    /**
     * @param int $position
     * @param string $name
     * @param ReflectionParameter $parameter
     * @return mixed|UploadedFile|null
     * @throws \ReflectionException
     */
    public function buildTypeValue(int $position, string $name, ReflectionParameter $parameter)
    {
        $data = null;
        foreach ($this->sourceFrom as $from) {
            $data = $this->getParameterValue($from, $position, $name, $parameter);
            if ($data !== null) {
                return $data;
            }
        }
        if ($parameter->allowsNull() && $data === null) {
            return null;
        }
        if ($data === null && $parameter->isDefaultValueAvailable() === false) {
            throw new InvalidArgumentException('parameter ' . $name . ' at ' . $position . ' could not be null', -32602);
        }
        return $parameter->getDefaultValue();
    }

    /**
     * @param string $from
     * @param int $position
     * @param string $name
     * @param ReflectionParameter $parameter
     * @return mixed|UploadedFile|null
     * @throws \ReflectionException
     */
    protected function getParameterValue(string $from, int $position, string $name, ReflectionParameter $parameter)
    {
        if ($parameter->getType() !== null) {
            if ($this->isPHPInnerType($parameter->getType())) {
                $data = $this->buildValueFrom($position, $name, $from);
                if ($data !== null && settype($data, $parameter->getType()) === true) {
                    return $data;
                }
            } else {
                return $this->buildObject($position, $name, $from, $parameter);
            }
        } else {
            return $this->buildValueFrom($position, $name, $from);
        }
        return null;
    }

    public function buildValueFrom(int $position, string $name, string $from)
    {
        if ($from === 'GET') {
            return $this->request->get($name);
        }
        if ($from === 'POST') {
            return $this->request->post($name);
        }
        if ($from === 'JSON' && $this->json !== null) {
            return $this->json[$position] ?? $this->json[$name] ?? null;
        }
        return null;
    }

    /**
     * @param int $position
     * @param string $name
     * @param string $from
     * @param ReflectionParameter $parameter
     * @return UploadedFile|null
     * @throws \ReflectionException
     */
    public function buildObject(int $position, string $name, string $from, ReflectionParameter $parameter)
    {
        $typeName = $parameter->getType()->__toString();
        $typeRef = new ReflectionClass($typeName);
        if ($typeRef->implementsInterface(MethodParameterInterface::class) || $typeRef->hasMethod('createParameterFromRequest')) {
            return $typeName::createParameterFromRequest($position, $name, $from, $this);
        }
        if ($typeRef->isSubclassOf(UploadedFile::class) && $from === 'POST') {
            return $this->request->file($name);
        }
        return null;
    }

    /**
     * Get 方法引用
     *
     * @return  ExportMethod
     */
    public function getMethod(): ExportMethod
    {
        return $this->method;
    }

    /**
     * Set 方法引用
     *
     * @param ExportMethod $method 方法引用
     *
     * @return  self
     */
    public function setMethod(ExportMethod $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get 是否含JSON数据
     *
     * @return  mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set 是否含JSON数据
     *
     * @param mixed $json 是否含JSON数据
     *
     * @return  self
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get 数据源
     *
     * @return  array
     */
    public function getSourceFrom()
    {
        return $this->sourceFrom;
    }

    /**
     * Set 数据源
     *
     * @param array $sourceFrom 数据源
     *
     * @return  self
     */
    public function setSourceFrom(array $sourceFrom)
    {
        $this->sourceFrom = $sourceFrom;

        return $this;
    }

    /**
     * Get 响应
     *
     * @return  Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Set 响应
     *
     * @param Request $request 响应
     *
     * @return  self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get 类引用
     *
     * @return  Application
     */
    public function getApplcation(): Application
    {
        return $this->applcation;
    }

    /**
     * Set 类引用
     *
     * @param Application $applcation 类引用
     *
     * @return  self
     */
    public function setApplcation(Application $applcation)
    {
        $this->applcation = $applcation;

        return $this;
    }
}
