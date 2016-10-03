<?php

/**
 * Interface Cache_Interface
 */
interface Cache_Interface
{
    /**
     * 设置
     * @param string $name
     * @param $value
     * @param int $expire
     * @return bool
     */
    public static function set(string $name, $value, int $expire=0):bool;
    /**
     * 获取
     * @param string $name
     * @return mixed
     */
    public static function get(string $name);
    /**
     * 删除
     * @param string $name
     * @return bool
     */
    public static function delete(string $name):bool;
    /**
     * 检测
     * @param string $name
     * @return bool
     */
    public static function has(string $name):bool;

    /**
     * 垃圾回收
     */
    public static function gc();
}
