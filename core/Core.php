<?php
use Core\Arr; // 引入Arr数组操纵类

spl_autoload_register(function ($name) {
    static $imported=[];
    if (isset($imported[$name])) return $imported[$name];
    $paths=[APP_LIB,CORE_PATH]; // 搜索目录
    $name=preg_replace('/[\\_]/', DIRECTORY_SEPARATOR, $name);
    foreach ($paths as $root) {
        // var_dump($require=$root.'/'.$name.'.php');
        if (file_exists($require=$root.'/'.$name.'.php')) {
            $imported[$name]=$require;
            require_once $require;
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
