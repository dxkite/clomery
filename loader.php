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
    defined('APP_VISIT') or define('APP_VISIT', '.visit.php');
    require_once CORE_PATH.'/Core.php';
    
    set_time_limit(0);
    date_default_timezone_set(conf('timezone','PRC'));

    View::loadCompile(); 
    require_once APP_ROOT.'/'.APP_VISIT;
    // Debug 模式 实时生成模板
    if (conf('DEBUG',0)==1) {
        View::theme('spider');
        View::compileAll();
    } 
    Page::display();
