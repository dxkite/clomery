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
     * @var Requset
     */
    protected $request;

    /**
     * 是否含JSON数据
     *
     * @var array|null
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

    public function getParameter():array
    {
        $method = $this->method->getReflectionMethod();
        $parameter = [];
        // 压入调用参数
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $position = $param->getPosition();
            $value = $this->buildTypeValue($position, $name, $param);
            if ($param->hasType()) {
                if (ContentWrapper::isTypeOf($value, $param->getType()) === false) {
                    throw new InvalidArgumentException('paramter '.$name.' at '.$position.' must be '. $param->getType(), -32602);
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
        return $this->json !== null ? ['JSON'] : [ 'POST','GET'];
    }

    public function isPHPInnerType(string $typeName):bool
    {
        return in_array($typeName, ['boolean','bool','integer','int','float','string','array']);
    }

    public function buildTypeValue(int $position, string $name, ReflectionParameter $parameter)
    {
        $data = null;
        foreach ($this->sourceFrom as  $from) {
            if ($parameter->getType() !== null) {
                if ($this->isPHPInnerType($parameter->getType())) {
                    $data = $this->buildValueFrom($position, $name, $from);
                    if (settype($data, $parameter->getType()) === false) {
                        continue;
                    }
                } else {
                    $data = $this->buildObject($position, $name, $from, $parameter);
                }
            } else {
                $data = $this->buildValueFrom($position, $name, $from);
            }
            if ($data !== null) {
                return $data;
            }
        }
        if ($data === null && $parameter->allowsNull() === false && $parameter->isDefaultValueAvailable() === false) {
            throw new InvalidArgumentException('paramter '.$name.' at '.$position.' could not be null', -32602);
        }
        return $parameter->isDefaultValueAvailable()? $parameter->getDefaultValue(): null;
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
    public function getMethod():ExportMethod
    {
        return $this->method;
    }

    /**
     * Set 方法引用
     *
     * @param  ExportMethod  $method  方法引用
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
     * @return  array|null
     */ 
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set 是否含JSON数据
     *
     * @param  array|null  $json  是否含JSON数据
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
     * @param  array  $sourceFrom  数据源
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
     * @return  Requset
     */ 
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set 响应
     *
     * @param  Requset  $request  响应
     *
     * @return  self
     */ 
    public function setRequest(Requset $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get 类引用
     *
     * @return  Application
     */ 
    public function getApplcation()
    {
        return $this->applcation;
    }

    /**
     * Set 类引用
     *
     * @param  Application  $applcation  类引用
     *
     * @return  self
     */ 
    public function setApplcation(Application $applcation)
    {
        $this->applcation = $applcation;

        return $this;
    }
}
