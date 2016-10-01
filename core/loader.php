<?php

    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__.'/..');
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    
    defined('CORE_PATH') or define('CORE_PATH', __DIR__);
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
    // 载入内置函数 PS:就是个自动加载，和配置加载
    require_once CORE_PATH.'/Core.php';
    // 设置PHP属性
    set_time_limit(conf('timelimit',0));
    // 设置时区
    date_default_timezone_set(conf('timezone','PRC'));
    // 载入页面编译器 -> |  后续添加钩子函数后添加到系统钩子
    View::loadCompile(); 
    // 载入页面URL配置规则
    require_once APP_ROOT.'/'.APP_VISIT;

    // Debug 模式 实时生成模板
    if (conf('DEBUG',0)==1) {
        // View::theme('spider');
        View::compileAll();
        
    } 
    // 开启Session
    Session::start();
    Site\Options::init();
    View::theme(Site\Options::getTheme());
    // 显示页面
    Page::display();
    // 写入Cookie
    Cookie::write();
    // 回收过期缓存
    Cache::gc();
