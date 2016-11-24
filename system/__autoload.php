<?php
spl_autoload_register('__autoload');

function __autoload(string $name)
{
    static $imported=[];
    if (isset($imported[$name])) {
        return $imported[$name];
    }

    $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $name);
    $paths=[__DIR__]; // 搜索目录

    foreach ($paths as $root) {
        // 优先查找文件
        if (file_exists($require=$root.'/'.$fname.'.php')) {
            $imported[$name]=$require;
            require_once $require;
            return $require;
        }
    }
}