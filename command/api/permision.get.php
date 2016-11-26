<?php
namespace api;
use Request;
use UserEntrance;

class Permision  extends UserEntrance
{
    public static function main(Request $request)
    {
        var_dump(self::$user);
    }
    public static function test()
    {
        $user=new \user\User;
        $user->_setVar(['name'=>'DXkite','password'=>'DXkite','email'=>'dxkite@atd3.cn']);
        $manager=new \user\Manager($user);
        $manager->signUp();
    }
}
