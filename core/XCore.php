<?php
    /** 可单独使用 Core 核心库 **/
    if (version_compare(PHP_VERSION, '7.0.0', '<')) {
        die('I Only Support PHP 7.0+ !'."\r\n");
    }
    // 核心依赖
    defined('CORE_VERSION') or define('CORE_VERSION', '1.0');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__);
    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__.'/..');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    // APP资源目录
    defined('APP_RES')or define('APP_RES', DOC_ROOT.'/res');
    // 配置文件名
    defined('APP_CONF') or define('APP_CONF', '.conf');
    defined('WEB_MIME') or define('WEB_MIME', '.mime');
    defined('APP_RECYCLE_BIN') or define('APP_RECYCLE_BIN', APP_RES.'/recycle_bin');
    // 备份
    defined('APP_BACKUP') or define('APP_BACKUP', APP_RES.'/backups');
    // 临时文件
    defined('APP_TMP')or define('APP_TMP',  APP_RES.'/tmp');
    // 不支持Rewrite模块的windows平台 > Apache 的bug https://bz.apache.org/bugzilla/show_bug.cgi?id=41441
    defined('IS_WINDOWS') or define('IS_WINDOWS', DIRECTORY_SEPARATOR === '\\');
    // 载入内置函数 PS:就是个自动加载，和配置加载
    require_once CORE_PATH.'/Core.php';
         // 设置PHP属性
    set_time_limit(conf('timelimit', 0));
    // 设置时区
    date_default_timezone_set(conf('timezone', 'PRC'));
