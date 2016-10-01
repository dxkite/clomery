<?php

/**
 * Interface Storage_Driver
 */
interface Storage_Driver
{
    // 文件类型
    /**
     *  文件是目录
     */
    const TYPE_DIR='dir';
    /**
     * FIFO
     */
    const TYPE_FIFO='fifo';
    /**
     * CHAR
     */
    const TYPE_CHAR='char';
    /**
     * 文件快
     */
    const TYPE_BLOCK='block';
    /**
     * 文件
     */
    const TYPE_FILE='file';
    /**
     * 链接
     */
    const TYPE_LINK='link';
    /**
     * 未知
     */
    const TYPE_UNKNOWN='unknown';

    // 创建文件夹

    /**
     * @param string $dirname
     * @param int $mode
     * @return bool
     */
    public static function mkdirs(string $dirname, int $mode=0777):bool;
    
    /**
     * @param string $dirname
     * @param int $mode
     * @return bool
     */
    public static function mkdir(string $dirname, int $mode=0777):bool;

    /**
     * @param string $dirname
     * @return mixed
     */
    public static function rmdir(string $dirname);

    /**
     * @param string $dir
     * @return mixed
     */
    public static function rmdirs(string $dir);

    /**
     * @param string $name
     * @param $content
     * @return bool
     */
    public static function put(string $name, $content):bool;

    /**
     * @param string $name
     * @return string
     */
    public static function get(string $name):string;

    /**
     * @param string $name
     * @return bool
     */
    public static function remove(string $name):bool;

    /**
     * @param string $name
     * @return bool
     */
    public static function isFile(string $name):bool;

    /**
     * @param string $dirs
     * @param bool $repeat
     * @param string $preg
     * @return array
     */
    public static function readDirFiles(string $dirs, bool $repeat=false, string $preg='/^.+$/'):array;

    /**
     * @param string $name
     * @return bool
     */
    public static function isDir(string $name):bool;

    /**
     * @param string $name
     * @return bool
     */
    public static function isReadable(string $name):bool;

    /**
     * @param string $name
     * @return int
     */
    public static function size(string $name):int;

    /**
     * @param string $name
     * @return int
     */
    public static function type(string $name):int;

    /**
     * @param string $file
     * @return bool
     */
    public static function exist(string $file) :bool;

    /**
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public static function copy(string $source, string $dest):bool;

}
