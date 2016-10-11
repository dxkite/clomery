<?php

/**
 * Class Upload
 * 文件上传管理
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
    public static function uploadFile(string  $name, int $uid, int $public=1, string $type=null):int
    {
        if ($_FILES[$name]['error']===0) {
            $type=$type?$type:pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
            return self::register($_FILES[$name]['tmp_name'], $type, $uid, $public);
        }
        return 0;
    }

    /**
     * 注册一个文件到上传文件目录
     * @param string $path 文件路径
     * @param int $uid 上传用户
     * @param int $public 是否公开 0否 1是
     * @return int
     */
    public static function register(string $file, string $type, int $uid, int $public=1):int
    {
        if (Storage::exist($file)) {
            $md5=md5_file($file);
            Storage::mkdirs(self::$root);
            if (Storage::move($file, self::$root.'/'.$md5) && ($q=new Query('INSERT INTO `#{uploads}` ( `owner`, `type`, `public`, `hash`) VALUES (:owner,:type,:public,:hash);'))->values(['owner'=>$uid, 'type'=>$type, 'public'=>$public, 'hash'=>$md5])->exec()) {
                return $q->lastInsertId();
            }
        }
        return -1;
    }

    /**
     * 根据ID获取公开文件路径
     * @param int $id
     * @return array
     */
    public static function getFileIfPublic(int $id):array
    {
        if ($get=(new Query('SELECT `type`,`owner`,`hash` as `md5`,`public` FROM `#{uploads}` WHERE `rid` = :rid AND `public`=1 LIMIT 1;'))->values(['rid'=>$id])->fetch()) {
            $get['path']=self::$root.'/'.$get['md5'];
            return $get;
        }
        return [];
    }

    /**
     * 根据ID获取文件路径
     * @param int $id
     * @return array
     */
    public static function getFile(int $id):array
    {
        if ($get=(new Query('SELECT `type`,`owner`,`hash` as `md5`,`public` FROM `#{uploads}` WHERE `rid` = :rid LIMIT 1;'))->values(['rid'=>$id])->fetch()) {
            $get['path']=self::$root.'/'.$get['md5'];
            return $get;
        }
        return [];
    }
    public static function outputPublic(int $id){
        $file=self::getFileIfPublic($id);
        Page::getController()->raw()->type($file['type']);
        echo Storage::get($file['path']);
    }
}
