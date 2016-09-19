<?php

class Storage
{
    public static $driver=null;
    public static function setDriver()
    {
        if (is_null(self::$driver)) {
            $driver=mini('Driver.File');
            self::$driver=new $driver;
        }
    }
}
