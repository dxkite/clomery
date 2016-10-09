<?php

/**
 * Class Upload
 */
class Upload
{
    /**
     * 设置储存目录
     * @param string $root
     */
    public static function setRoot(string $root)
    {
        self::$root = $root;
    }

    /**
     * 储存目录
     * @var string
     */
    public static $root=APP_RES.'/uploads';

    /**
     * 从表单上传中创建文件
     * @param string $name 表单名
     * @param int $uid 上传用户
     * @param int $public 是否公开
     * @return bool
     */
    public static function uploadFile(string  $name, int $uid, int $public=1):bool
    {

    }

    /**
     * 注册一个文件到上传文件目录
     * @param string $path 文件路径
     * @param int $uid 上传用户
     * @param int $public 是否公开 0否 1是
     * @return bool
     */
    public static function register(string $path, int $uid, int $public=1):bool
    {

    }

    /**
     * 根据ID获取公开文件路径
     * @param int $id
     * @return string
     */
    public static function getPathIfPublic(int $id):string
    {

    }

    /**
     * 根据ID获取文件路径
     * @param int $id
     * @return string
     */
    public static function getPath(int $id):string
    {

    }
}
