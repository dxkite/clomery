<?php
namespace Site;

class Page
{
    protected static $var;
    public function __get(string $name)
    {
        return isset(self::$var[$name])?self::$var[$name]:NULL;
    }

    public function __set(string $name, $value)
    {
        return self::$var[$name]=$value;
    }
}
