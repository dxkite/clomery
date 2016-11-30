<?php

require_once __DIR__.'/../system/initailze.php';


$class=new ReflectionClass($argv[1]);
foreach ( $class->getMethods() as $method)
{
    $param_list=[];
    foreach ($method->getParameters() as $parameter){
        $value=$parameter->getName();
        if ($parameter->isDefaultValueAvailable()){
            $value=[$value,$parameter->getDefaultValue()]; 
        }
        if ($parameter->hasType()){
            $param_list[$parameter->getType().'']=$value;
        }
        else{
            $param_list[]=$value;
        }
    }
   $param = var_export($param_list,true);
   $callback=$method->class.'::'.$method->name;
   $tpl="return api_check_values(\$param,$param,'$callback');";
   var_dump($tpl);
}