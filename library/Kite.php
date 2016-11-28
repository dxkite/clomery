<?php
require_once __DIR__.'/../system/initailze.php';
require_once __DIR__.'/functions.php';

class Kite
{
    public static $request;
    public static $page;
    public function init()
    {
        template\Manager::loadCompile();
        template\Manager::compileAll();
        self::$request=new Request();
        Router::dispatch(self::$request);
    }
}
