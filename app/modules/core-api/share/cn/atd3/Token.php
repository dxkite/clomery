<?php
namespace cn\atd3;

use suda\core\Request;
use suda\core\Cookie;
use suda\core\Hook;

class Token
{
    private static $values=[];
    private static $listen=false;
    private static $expire=0;
    
    public static function set(string $name, string $value, int $expire=0)
    {
        if (!self::$listen) {
            Hook::listen('display:output', 'Token::generate');
            self::$listen=true;
        }
        self::$values[$name]=$value;
        self::$expire=$expire;
        return Cookie::set('token_'.$name, $value, $expire)->httpOnly();
    }

    public static function get(string $name, string $default='')
    {
        if (isset(self::$values[$name])) {
            return self::$values[$name];
        }
        // 如果请求为JSON，从JSON中获取令牌
        if (Request::isJson()) {
            $value=Request::json();
            if (is_array($value['token'])) {
                self::$values=array_merge(self::$values, $value['token']);
                if (isset($value['token'][$name])) {
                    return $value['token'][$name];
                }
            }
        }
        // JSON获取不到，或者为页面请求时，从Cookie中获取
        return Cookie::get('token_'.$name, $default);
    }

    public static function has(string $name)
    {
        if (Request::isJson()) {
            $value=Request::json();
            // var_dump(isset($value['token'][$name]));
            return isset($value['token'][$name]);
        } 
        return Cookie::has('token_'.$name);
    }

    public static function generate(string &$output, string $type)
    {
        if ($type==='json' && $values=\suda\tool\Json::decode($output, true)) {
            foreach (self::$values as $name=>$value) {
                $values['token'][$name]=$value;
            }
            if (self::$expire) {
                if (isset($values['token'])) {
                    $values['token']['expire']=self::$expire;
                }
            }
            $output=json_encode($values);
        }
    }
}
