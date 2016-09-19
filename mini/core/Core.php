<?php
use Core\Arr;

// 获取配置
function mini(string $name,mixed $default)
{
    return Arr::get(Storage::parseIniFile(APP_PATH.'/'.MINI_INI,true),$name,$default);
}