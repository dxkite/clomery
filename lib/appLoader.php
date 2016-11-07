<?php
    require_once __DIR__.'/../core/XCore.php';

    // APP 相关数据
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    // 语言文件夹
    defined('APP_LANG')or define('APP_LANG', APP_RES.'/lang');
    // 视图控制
    defined('APP_VIEW')or define('APP_VIEW', APP_RES.'/view');
    defined('APP_TPL')or define('APP_TPL', APP_RES.'/tpl');
    // 临时文件
    defined('APP_TMP')or define('APP_TMP',  APP_RES.'/tmp');

    defined('INSTALL_LOCK') or define('INSTALL_LOCK', 'install.lock');
    defined('APP_VISIT') or define('APP_VISIT', '.visit.php');

    // 系统开始
    Event::shift('System_Boot', true)->call();

    // 载入页面编译器 -> |  后续添加钩子函数后添加到系统钩子
    View::loadCompile();
    // 开启Session
    Session::start();

        // 获取网站设置
    Site_Options::init();
    $op=new Site_Options;
    
    // 语言支持
    Page::language(Cookie::get('lang', 'zh-CN'));
    View::theme(Site_Options::getTheme());

    // Debug 模式 实时生成模板
    if (conf('DEBUG', 0)==1) {
        View::compileAll();
    }
    // 关闭网站后只能手动开启
    if ($op->site_close==0) {
        // 载入页面URL配置规则
        require_once APP_ROOT.'/'.APP_VISIT;
    } else {
        Page::visit('/theme/{path}', 'View::file')->with('path', '/^(.+)$/')->id('theme')->override()->age(10000)->close();
        Page::global('_Op', $op);
        Page::visit('/{path}?', null)->with('path', '/^.*?$/')->use('close');
    }
    
    Event::pop('System_Before_Display')->call();
    // 显示页面
    Page::display();
    Event::pop('System_After_Display')->call();
    // 系统结束
    Event::pop('System_Exit')->call();
    // 写入Cookie
    Cookie::write();
    // 回收过期缓存
    Cache::gc();
