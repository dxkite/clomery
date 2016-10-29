<?php

class System
{
    //  登陆的用户
    public static $user=null;

    public function user()
    {
        if (is_null(self::$user)) {
            self::$user=new User;
        }
        return self::$user;
    }
    
    public static function checkInstallStatus()
    {
    }
}
