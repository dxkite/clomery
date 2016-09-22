<?php

    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/core');
    // APP 相关数据
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    // APP资源目录
    defined('APP_RES')or define('APP_RES', DOC_ROOT.'/res');
    // 视图控制
    defined('APP_VIEW')or define('APP_VIEW', APP_RES.'/view');
    defined('APP_TPL')or define('APP_TPL', APP_RES.'/tpl');
    // 配置文件名
    defined('APP_CONF') or define('APP_CONF', '.conf');
    
    
    require_once CORE_PATH.'/Core.php';
    // View::compile('head');
    // View::compile('index');
    // $caller=new Core\Caller(['View', 'render']);
    // $caller->call(['index',['infoelse'=>'Info INFO Set']]);


    
    Page::visit('/{id}/{name}/', function ($id, $name) {
        echo 'OK ==> ', $id, $name;
    })
    ->with('id', 'int')
    ->with('name', 'string')
    ->name('main');

    Page::visit('/',function()
    {
        View::set('hello','main page');
    });
    Page::visit('/hello', function () {
        echo 'OK hello page';
    });
    Page::default(function () {
         echo '__default__';
    });
    Page::hand();
    // var_dump(Route::$maps);
