<?php

    defined ('APP_PATH') or define ('APP_PATH',dirname(__DIR__).'/app');
    defined ('MINI_INI') or define ('MINI_INI','.mini.ini');
    defined ('CORE_PATH') or define ('CORE_PATH',__DIR__.'/core');
    
    $ini=parse_ini_file('D:\Server\three\three\.mini.ini',true);
    require_once CORE_PATH.'/Core.php';
    Storage::put('hello.txt','Hello World');