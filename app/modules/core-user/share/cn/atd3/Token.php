<?php
namespace cn\atd3;

use suda\core\Cookie;

class Token
{
    private static $values=[];
    private static $listen=false;
    private static $expire=0;
    
    public static function set(string $name, string $value, int $expire=0)
    {
        return Cookie::set('token_'.$name, $value, $expire)->httpOnly();
    }

    public static function get(string $name, string $default='')
    {
        if (isset(self::$values[$name])) {
            return self::$values[$name];
        }
        return Cookie::get('token_'.$name, $default);
    }

    public static function has(string $name)
    {
        return Cookie::has('token_'.$name);
    }
}
