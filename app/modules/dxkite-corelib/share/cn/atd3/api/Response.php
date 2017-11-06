<?php
namespace cn\atd3\api;
use cn\atd3\api\response\OnCallableResponse;
use suda\core\route\Mapping;

class Response extends OnCallableResponse
{
    public function getExportMethods($class=NULL)
    {
        $param=Mapping::$current->getParam();
        $class=class_name($param['proxyClass']);
        return parent::getExportMethods(new $class($this->getContext()));
    }
}