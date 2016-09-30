<?php

class Session
{
    public static $fname=[
        'id'=>'id',
        'cacheExpire'=>'cache_expire',
        'cacheLimiter'=>'cache_limiter',
        'setCookieParams'=>'set_cookie_params',
    ];
    public static function start()
    {
        $path=APP_RES.'/'.conf('Session.save_path');
        Storage::mkdirs($path);
        session_save_path($path);
        session_name(conf('Session.name', 'atd_sid'));
        session_cache_limiter(conf('Session.limiter', 'private'));
        session_cache_expire(conf('Session.expire', '60'));
        session_start();
    }
    public static function set(string $name, $value)
    {
        $_SESSION[$name]=$value;
        return isset($_SESSION[$name]);
    }
    public static function get(string $name)
    {
        return $_SESSION[$name];
    }
    public static function __callStatic(string $name, array $params)
    {
        if (array_key_exists($name, self::$fname)) {
            return call_user_func_array('session_'.self::$fname[$name], $params);
        }
    }
}
