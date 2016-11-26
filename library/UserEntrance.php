<?php

abstract class UserEntrance implements Entrance
{
    public static $user;
    public static function beforeRun(Request $request)
    {
        if (Cookie::has('token')) {
            $token=new user\Token();
            $token->token=Cookie::get('token');
            $manager=new archive\Manager($token);
            if ($token=$manager->where(['expire'=>['>',time()]])->retrieve()->fetch()){

            }
        } else {

        }
    }
    abstract public static function main(Request $request);
    public static function afterRun($return)
    {
    }
}
