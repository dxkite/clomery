<?php

class Storage
{
    private static $driver=null;
    private static function setDriver()
    {
        if (is_null(self::$driver)) {
            $driver='Storage_Driver_'. conf('Driver.Storage','File');
            self::$driver=new $driver;
        }
    }
    public static function  __callStatic($method,$args)
    {
        self::setDriver();
        return forward_static_call_array([self::$driver,$method],$args);
    }
}
