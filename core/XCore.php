<?php
    /** 可单独使用 Core 核心库 **/
    if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            die('I Only Support PHP 7.0+ !');
    }
    // 核心依赖
    defined('CORE_VERSION') or define('CORE_VERSION', '1.x-dev');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__);
    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__.'/..');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    // 配置文件名
    defined('APP_CONF') or define('APP_CONF', '.conf');
    defined('WEB_MIME') or define('WEB_MIME', '.mime');
    // 载入内置函数 PS:就是个自动加载，和配置加载
    require_once CORE_PATH.'/Core.php';
         // 设置PHP属性
    set_time_limit(conf('timelimit', 0));
    // 设置时区
    date_default_timezone_set(conf('timezone', 'PRC'));
