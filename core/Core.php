<?php
use Core\Arr; // 引入Arr数组操纵类

spl_autoload_register(function ($name) {
    static $imported=[];
    if (isset($imported[$name])) {
        return $imported[$name];
    }
    $paths=[APP_LIB, CORE_PATH, APP_ROOT]; // 搜索目录
    $name=preg_replace('/[\\_]/', DIRECTORY_SEPARATOR, $name);
    foreach ($paths as $root) {
        // 优先查找文件
        if (file_exists($require=$root.'/'.$name.'.php')) {
            $imported[$name]=$require;
            require_once $require;
        }
        // 其次查找目录配驱动
        elseif (is_dir($root.'/'.$name)) {
            // 配置存在
            if (conf('Driver.'.$name) && file_exists($require="{$root}/{$name}/".conf('Driver.'.$name)."_{$name}.php")) {
                require_once $require;
            }
        }
    }
});

// 获取配置
function conf(string $name, $default=null)
{
    static $conf=null;
    if (is_null($conf)) {
        $conf=parse_ini_file(DOC_ROOT.'/'.APP_CONF, true);
    }
    return Arr::get($conf, $name, $default);
}
// 获取MIME
function mime(string $name, $default=null)
{
    static $mime=null;
    if (is_null($mime)) {
        $mime=parse_ini_file(DOC_ROOT.'/'.WEB_MIME);
    }
    return Arr::get($mime, $name, $default);
}


