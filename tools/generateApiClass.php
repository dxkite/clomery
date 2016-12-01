<?php

require_once __DIR__.'/../system/initailze.php';

function generateApiClass(string $name,string $interface,string $namespace,string $comment,string $permission='')
{
    ob_start();
    include __DIR__ .'/tpl/json.php';
    $content=ob_get_clean();
    echo $content;
}

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
   $tpl="return api_check_callback(\$param,$param,'$callback');";
   $comment=$method->getDocComment();
   echo $comment;
   $namespace=preg_replace('/\\\\+/','\\','\\'.str_replace('model','',$class->getName()));
   $namespace=$namespace==='\\'?'':$namespace;
   generateApiClass($method->getName(),$tpl,strtolower($namespace),$comment,'');
}