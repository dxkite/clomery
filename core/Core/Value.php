<?php
namespace Core;
class Value
{
    protected static $var;
    public function __construct($var)
    {
        self::$var=$var;
    }
    public function __get(string $name)
    {
        return isset(self::$var[$name])?self::$var[$name]:NULL;
    }

    public function __set(string $name, $value)
    {
        return self::$var[$name]=$value;
    }
    public function __isset(string $name)
    {
        return isset(self::$var[$name]);
    }
    public function __call(string $name,$args)
    {
        $fmt=isset(self::$var[$name])?self::$var[$name]:$args[0];
        if (count($args)>1)
        {
            $args=array_slice($args,1);
            return sprintf($fmt,$args);
        }
        return $fmt;
    }
}