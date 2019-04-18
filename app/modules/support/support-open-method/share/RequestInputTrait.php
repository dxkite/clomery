<?php
namespace support\openmethod;

use ReflectionClass;
use support\openmethod\MethodParameterBag;



/**
 * 从Request中获取数据
 */
trait RequestInputTrait
{
    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $json = $bag->getJson();
        if ($from === 'JSON' && $json !== null && \is_array($json) && \array_key_exists($name, $json)) {
            if ($bag->getMethod()->getReflectionMethod()->getNumberOfParameters() === 1) {
                return static::createFromRequest($json);
            }
            return static::createFromRequest($json[$name]);
        }
        if ($from === 'POST') {
            $request = $bag->getRequest();
            if ($bag->getMethod()->getReflectionMethod()->getNumberOfParameters() === 1) {
                return static::createFromRequest($request->post());
            }
            if ($request->hasPost($name)) {
                $data = $request->post($name);
                return static::createFromRequest($data);
            }
        }
        return null;
    }

    protected static function createFromRequest(array $data) {
        $reflectClass = new \ReflectionClass(static::class);
        $object = $reflectClass->newInstance();
        if (\method_exists($object, '__set')) {
            static::setValueWithMagicSet($object, $data);
        } else {
            static::setValueWithReflection($reflectClass, $object, $data);
        }
        return $object;
    }
    
    /**
     * 通过反射方法设置值
     *
     * @param \ReflectionClass $reflectClass
     * @param mixed $object
     * @param array $data
     * @return void
     */
    protected static function setValueWithReflection(\ReflectionClass $reflectClass, $object, array $data)
    {
        foreach ($data as $name => $value) {
            if ($reflectClass->hasProperty($name)) {
                $property = $reflectClass->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
        }
    }

    /**
     * 通过魔术方法设置值
     *
     * @param \ReflectionClass $reflectClass
     * @param mixed $object
     * @param array $data
     * @return void
     */
    protected static function setValueWithMagicSet($object, array $data)
    {
        foreach ($data as $name => $value) {
            $object->__set($name, $value);
        }
    }
}
