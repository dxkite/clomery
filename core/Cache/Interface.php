<?php

interface Cache_Interface
{
    // 设置
    public static function set(string $name, $value, int $expire=1440);
    // 获取
    public static function get(string $name);
    // 删除
    public static function delete(string $name);
    // 检测
    public static function has(string $name);
    // 替换
    public static function replace(string $name, $value, int $expire=0);
    // 垃圾回收
    public static function gc();
}
