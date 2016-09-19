<?php

    defined ('APP_PATH') or define ('APP_PATH',dirname(__DIR__).'/app');
    defined ('MINI_INI') or define ('MINI_INI','.mini.ini');
    
    $ini=parse_ini_file('D:\Server\three\three\.mini.ini',true);
    require_once 'core/Core/Arr.php';
    $arr=Core\Arr::toString('hello',['a'=>[1,2,3,4],5,4,5]);
