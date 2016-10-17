<?php
    // 核心版本
    defined('CORE_VERSION') or define('CORE_VERSION', '1.x-dev');
    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__.'/..');
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__);
    // APP 相关数据
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    // APP资源目录
    defined('APP_RES')or define('APP_RES', DOC_ROOT.'/res');
    // 语言文件夹
    defined('APP_LANG')or define('APP_LANG', APP_RES.'/lang');
    // 视图控制
    defined('APP_VIEW')or define('APP_VIEW', APP_RES.'/view');
    defined('APP_TPL')or define('APP_TPL', APP_RES.'/tpl');
    // 临时文件
    defined('APP_TMP')or define('APP_TMP',  APP_RES.'/tmp');
    // 配置文件名
    defined('APP_CONF') or define('APP_CONF', '.conf');
    defined('WEB_MIME') or define('WEB_MIME', '.mime');
    defined('INSTALL_LOCK') or define('INSTALL_LOCK', 'install.lock');
    defined('APP_VISIT') or define('APP_VISIT', '.visit.php');
    if (version_compare(PHP_VERSION,'7.0.0','<')){
        die('I Only Support PHP 7.0+ !');
    }
    // 载入内置函数 PS:就是个自动加载，和配置加载
    require_once CORE_PATH.'/Core.php';
    // 设置PHP属性
    set_time_limit(conf('timelimit', 0));
    // 设置时区
    date_default_timezone_set(conf('timezone', 'PRC'));
    // 系统开始
    Event::shift('System_Boot', true)->call();

    // 载入页面编译器 -> |  后续添加钩子函数后添加到系统钩子
    View::loadCompile();
    // 开启Session
    Session::start();
     // Debug 模式 实时生成模板
    if (conf('DEBUG', 0)==1) {
        View::compileAll();
    }
    // 语言支持
    Page::language(Cookie::get('lang', 'zh_cn'));
    View::theme(Site_Options::getTheme());

    // 获取网站设置
    Site_Options::init();
    $op=new Site_Options;
    if ($op->site_close==0) {
        // 载入页面URL配置规则
        require_once APP_ROOT.'/'.APP_VISIT;
    } else {
        Page::visit('/resource/{path}', function ($path_raw) {
            $type=pathinfo($path_raw, PATHINFO_EXTENSION);
            $path_raw=rtrim($path_raw, '/');
            if (Storage::exist(APP_VIEW.'/'.$path_raw)) {
                Page::getController()->raw()->type($type);
                echo Storage::get(APP_VIEW.'/'.$path_raw);
            } else {
                Page::error404($path_raw);
            }
        })->with('path', '/^(.+)$/')->id('resource')->override();
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
