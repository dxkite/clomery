<?php

    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/core');
    // APP 相关数据
    defined('APP_INI') or define('APP_INI', '.app.ini');
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    // APP资源目录
    defined('APP_RES')or define('APP_RES',DOC_ROOT.'/res');
    // 视图控制
    defined('APP_VIEW')or define('APP_VIEW',APP_RES.'/view');
    defined('APP_TPL')or define('APP_TPL',APP_RES.'/tpl');
    // 配置文件名
    defined('APP_INI') or define('APP_INI', '.app.ini');
    
    
    require_once CORE_PATH.'/Core.php';
    View::compile('head');
    View::compile('index');
    $caller=new Core\Caller(['View', 'render']);
    $caller->call(['index',['infoelse'=>'Info INFO Set']]);
    //  Env::Options('Hello', '<h1>World</h1>')->hello();


    function csrf_field()
    {
        return '<h1>hello word</h1>';
    }