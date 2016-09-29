<?php
use Core\CookieSetter as Setter;

class Cookie
{
    public static $values=[];
    public static function set(string $name, string $value)
    {
        self::$values[$name]=new Setter($name, $value);
        return self::$values[$name];
    }
    public static function get(string $name)
    {
        return isset(self::$values[$name])?self::$values[$name]->get():isset($_COOKIE[$name])?$_COOKIE[$name]:null;
    }
    public static function write()
    {
        foreach (self::$values as $setter) {
            $setter->set();
        }
    }
}
