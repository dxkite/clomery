<?php
namespace Env;

class Options implements \Env_Method
{
    

    function setParams(array $params)
    {
        var_dump($params);
    }
    function hello()
    {
        echo 'hello';
    }
    
}