<?php
namespace support\openmethod;

use ReflectionClass;
use ReflectionMethod;



class ExportMethod  implements \JsonSerializable
{
    
    /**
     * 类和对象
     *
     * @var string|object
     */
    protected $class;

    /**
     * 方法名
     *
     * @var string
     */
    protected $method;

    /**
     * 方法引用
     *
     * @var ReflectionMethod
     */
    protected $reflectionMethod;

    /**
     * 类引用
     *
     * @var ReflectionClass
     */
    protected $reflectionClass;
    
    /**
     * 创建导出类
     *
     * @param string|object $class
     * @param string $method
     * @param \ReflectionMethod $reflectionMethod
     */
    public function __construct($class,string $method,ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod) {
        $this->class = $class;
        $this->method = $method;
        $this->reflectionClass = $reflectionClass;
        $this->reflectionMethod = $reflectionMethod;
    }

    public function jsonSerialize() {
        return [ 'class' => is_string($this->class)? $this->class : \get_class($this->class), 'method' => $this->method];
    }

    /**
     * Get 方法引用
     *
     * @return  ReflectionMethod
     */ 
    public function getReflectionMethod():ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    /**
     * Set 方法引用
     *
     * @param  ReflectionMethod  $reflectionMethod  方法引用
     *
     * @return  self
     */ 
    public function setReflectionMethod(ReflectionMethod $reflectionMethod)
    {
        $this->reflectionMethod = $reflectionMethod;

        return $this;
    }

    /**
     * Get 方法名
     *
     * @return  string
     */ 
    public function getMethod():string
    {
        return $this->method;
    }

    /**
     * Set 方法名
     *
     * @param  string  $method  方法名
     *
     * @return  self
     */ 
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get 类和对象
     *
     * @return  string|object
     */ 
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set 类和对象
     *
     * @param  string|object  $class  类和对象
     *
     * @return  self
     */ 
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get 类引用
     *
     * @return  ReflectionClass
     */ 
    public function getReflectionClass():ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * Set 类引用
     *
     * @param  ReflectionClass  $reflectionClass  类引用
     *
     * @return  self
     */ 
    public function setReflectionClass(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
    }
}
