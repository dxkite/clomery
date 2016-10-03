<?php
namespace Core;
class Value
{
    protected  $var;
    public function __construct($var)
    {
        $this->var=$var;
    }
    public function __get(string $name)
    {
        return isset($this->var[$name])?$this->var[$name]:'undefined:'.$name;
    }

    public function __set(string $name, $value)
    {
        return $this->var[$name]=$value;
    }
    public function __isset(string $name)
    {
        return isset($this->var[$name]);
    }
    public function __call(string $name,$args)
    {
        $fmt=isset($this->var[$name])?$this->var[$name]:isset($args[0])?$args[0]:'undefined:'.$name;
        if (count($args)>1)
        {
            $args[0]=$fmt;
            return call_user_func_array('sprintf',$args);
        }
        return $fmt;
    }
}