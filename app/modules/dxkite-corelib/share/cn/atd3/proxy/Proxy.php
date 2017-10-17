<?php
namespace cn\atd3\proxy;
use cn\atd3\proxy\exception\ProxyException;
use ReflectionMethod;

class Proxy {
    private $object;
    
    public function __construct(ProxyObject $object){
        $this->object=$object;
    }
    
    private static function call(ProxyObject $object,string $method,array $param_arr=[]){
        if($object->getContext()->getVisitor()->canAccess([$object,$method])){
            $methodrefer=new ReflectionMethod($object,$method);
            return $methodrefer->invokeArgs($object, $param_arr);
        }
        throw new ProxyException(__('permission deny'));
    }

    public function __call(string $method,array $param_arr){
        return self::call($this->object,$method,$param_arr);
    }
}