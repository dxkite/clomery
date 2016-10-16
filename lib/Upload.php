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
    public static $uid=0;
    /**
     * 从表单上传中创建文件
     * @param string $name 表单名
     * @param int $uid 上传用户
     * @param int $public 是否公开
     * @return bool
     */
    
    public static function uploadFile(string  $name, int $public=1, string $type=null):int
    {
        if ($_FILES[$name]['error']===0) {
            $type=$type?$type:pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
            return self::register($_FILES[$name]['name'], $_FILES[$name]['tmp_name'], $type,$public);
        }
        return 0;
    }


    
    public static function uploadString(string $content, string $name, string $type, int $public=1)
    {
        $file=APP_TMP.'/upload_'.base64_encode(time().$name).'.tmp';
        if (Storage::put($file, $content)) {
            $id=self::register($name,$file,$type,$public);
            Storage::remove($file);
            return $id;
        }
    }
    /**
     * 注册一个文件到上传文件目录
     * @param string $path 文件路径
     * @param int $uid 上传用户
     * @param int $public 是否公开 0否 1是
     * @return int
     */
    public static function register(string $name, string $file, string $type,int $public=1):int
    {
        if (Storage::exist($file)) {
            $md5=md5_file($file);
            Storage::mkdirs(self::$root);
            if (Storage::move($file, $file=self::$root.'/'.$md5)) {
                $id=0;
                try {
                    Query::beginTransaction();
                    // 插入文件所有总表
                    $q=new Query('INSERT INTO `#{upload_resource}` ( `type`,`hash`, `reference`) VALUES (:type,:hash,1);');
                    $resource=0;
                    if ($q->values(['type'=>$type, 'hash'=>$md5])->exec()) {
                        $resource=$q->lastInsertId();
                    } else {
                        $resource=$q->query('SELECT `rid` FROM `#{upload_resource}` WHERE `hash` = :hash', ['hash'=>$md5])->fetch()['rid'];
                        $q->query('UPDATE `#{upload_resource}` SET `reference`=  `reference` +1 WHERE `rid`=:rid', ['rid'=>$resource])->exec();
                    }
                    // 确保文件为一个
                    if ($qid=$q->query('SELECT `rid` FROM `#{uploads}` WHERE `resource`=:resource LIMIT 1;', ['resource'=>$resource])->fetch()) {
                        $id=$qid['rid'];
                    } else {
                        $q->query('INSERT INTO `#{uploads}` ( `owner`,`name`,`extension`,`time`, `resource`,`public`) VALUES (:owner,:name,:extention,:time,:resource,:public);');
                        $q->values(['owner'=>self::$uid, 'name'=>pathinfo($name,PATHINFO_FILENAME), 'extention'=>$type,'time'=>time(), 'resource'=>$resource, 'public'=>$public])->exec();
                        $id=$q->lastInsertId();
                    }
                    Query::commit();
                } catch (\Exception $e) {
                    Query::rollBack();
                    Storage::remove($file);
                    return -2;
                }
                return $id;
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
        if ($get=(new Query('SELECT `name`,`time`,`hash` as `md5`,`owner`,`type` FROM `#{uploads}` JOIN  `#{upload_resource}` ON `#{upload_resource}`.`rid`=`resource` WHERE `#{uploads}`.`rid` =:rid AND `public`=1 LIMIT 1;'))->values(['rid'=>$id])->fetch()) {
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
        if ($get=(new Query('SELECT `name`,`time`,`hash` as `md5`,`owner`,`type` FROM `#{uploads}` JOIN  `#{upload_resource}` ON `#{upload_resource}`.`rid`=`resource` WHERE `#{uploads}`.`rid` =:rid LIMIT 1;'))->values(['rid'=>$id])->fetch()) {
            $get['path']=self::$root.'/'.$get['md5'];
            return $get;
        }
        return [];
    }
    public static function outputPublic(int $id, bool $download=false)
    {
        $file=self::getFileIfPublic($id);
        if (count($file)) {
            Page::getController()->raw()->type($file['type']);
            if ($download) {
                header('Content-Disposition:attachment;filename='.$file['name']);
            }
            echo Storage::get($file['path']);
        } else {
            echo 'No Resource';
        }
    }

    /**
     * @return int
     */
    public static function getUid(): int
    {
        return self::$uid;
    }

    /**
     * @param int $uid
     */
    public static function setUid(int $uid)
    {
        self::$uid = $uid;
    }
}
