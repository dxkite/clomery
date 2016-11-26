<?php
namespace api;

class Permision  implements \Entrance
{
    public static function beforeRun(\Request $request){}
    public static function afterRun($return){}
    public static function main(\Request $request)
    {
        echo 'get';
    }
}
