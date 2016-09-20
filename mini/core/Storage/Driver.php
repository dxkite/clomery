<?php
interface Storage_Driver
{
    // 文件类型
    const TYPE_DIR='dir';
    const TYPE_FIFO='fifo';
    const TYPE_CHAR='char';
    const TYPE_BLOCK='block';
    const TYPE_FILE='file';
    const TYPE_LINK='link';
    const TYPE_UNKNOWN='unknown';

    // 创建文件夹
    public static function mkdirs(string $dirname, int $mode=0777);
    public static function mkdir(string $dirname, int $mode=0777);
    public static function rmdir(string $dirname);
    public static function rmdirs(string $dir);

    public static function put(string $name, $content);
    public static function get(string $name);
    public static function remove(string $name);

    public static function isFile(string $name);
    public static function isDir(string $name);
    public static function isReadable(string $name);
    public static function size(string $name);
    public static function type(string $name);
    public static function exsit(string $file);

}
