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
    defined('WEB_MIME') or define('WEB_MIME', '.mime');
    
    require_once CORE_PATH.'/Core.php';


    Page::visit('/{article}', function ($article) {
        echo "OK hello page:$article";
    })
    ->with('article','int')->name('article');
    
    Page::visit('/{id}/{name}', function ($id, $name) {
        echo 'OK ==> ', $id, $name;
    })
    ->with('id', 'int')
    ->with('name', 'string')
    ->name('main');

    Page::visit('/getUser/{id}',function($id=0)
    {
        return (new Qurey('SELECT * FROM `#{users}` WHERE `uid`=:uid LIMIT 1;',['uid'=>$id]))->fetch();
    })
    ->with('id','int')
    ->json();
    
    Page::default(function () {
         echo '__default__';
    });
    Page::display();
