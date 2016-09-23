<?php

class Env
{
    // View echo
    public static function echo()
    {
        foreach (func_get_args() as $arg)
        {
            echo htmlspecialchars($arg);
        }
    }
    // View Includer 
    public static function include()
    {
        $include= new View\Includer();
        $include->setParams(func_get_args());
        return $include;
    }
    public static function url(string $name, array $args=[])
    {
        return Page::url($name,$args);
    }
    // 载入接口 Env::接口名 
    public static function __callStatic($method, $args)
    {
        $classname = 'Env\\'.$method;
        $class=new $classname();
        $class->setParams($args);
        return $class;
    }
}
