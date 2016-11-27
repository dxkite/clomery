<?php

define('SITE_CMD', __DIR__.'/../command');
define('SITE_RESOURCE', __DIR__.'/../resource');
define('SITE_TEMPLATE', SITE_RESOURCE.'/template');
define('SITE_VIEW', SITE_RESOURCE.'/view');
define('SITE_VERSION', '2.0.0');
define('SITE_CONFIG', SITE_RESOURCE.'/config.php');
define('SITE_LIB', __DIR__.'/../library');
define('SITE_TEMP', SITE_RESOURCE.'/tmp');

spl_autoload_register('import');

function import(string $name)
{
    static $imported=[];
    if (isset($imported[$name])) {
        return $imported[$name];
    }

    $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $name);
    $paths=[__DIR__,SITE_LIB]; // 搜索目录

    foreach ($paths as $root) {
        // 优先查找文件
        if (file_exists($require=$root.'/'.$fname.'.php')) {
            $imported[$name]=$require;
            require_once $require;
            return $require;
        }
    }
}

function conf(string $name, $default=null)
{
    static $conf;
    if (!$conf) {
        if (file_exists(SITE_CONFIG)) {
            $conf=require_once SITE_CONFIG;
        }
    }
    return helper\ArrayHelper::get($conf, $name, $default);
}

function mime(string $name='')
{
    static $mime;
    if (!$mime) {
        $mime=parse_ini_file(__DIR__.'/type.mime');
    }
    if ($name) {
        return isset($mime[$name])?$mime[$name]:'text/plain';
    } else {
        return  $mime;
    }
}
// 设置PHP属性
set_time_limit(conf('timelimit', 0));
// 设置时区
date_default_timezone_set(conf('timezone', 'PRC'));