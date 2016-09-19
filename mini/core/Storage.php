<?php

class Storage
{
    public static $driver=null;
    public static function setDriver()
    {
        if (is_null(self::$driver)) {
            $driver='Storage\Driver\\'. mini('Driver.Storage','File');
            self::$driver=new $driver;
        }
    }
    public static function  __callStatic($method,$args)
    {
        self::setDriver();
        forward_static_call_array([self::$driver,$method],$args);
    }
}
