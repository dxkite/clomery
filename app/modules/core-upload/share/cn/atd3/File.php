<?php
namespace cn\atd3;

use suda\core\Storage;

/**
 * Class File
 * 上传文件管理
 */
class File
{
    public static $root=DATA_DIR.'/uploads';
    public static function upload(int $uid, string $path, string $name, int $size, int $state=Upload::STATE_PUBLISH)
    {
        $root=Storage::path(self::$root);
        $type=pathinfo($name, PATHINFO_EXTENSION);
        $hash=md5_file($path);
        $cp=Storage::copy($path, $root.'/'.$hash);
        if ($cp) {
            return Upload::create($uid, $name, $size, $type, $hash, $state);
        }
        return 0;
    }
    public static function path(string $hash)
    {
       return self::$root.'/'.$hash;
    }
}
