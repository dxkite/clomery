<?php
namespace api;

use api\Visitor;
use api\Param;
use model\Upload as MUpload;
use Storage;
use User;

class Upload extends Visitor
{
    public $auth='upload'; // 要有文件上传权限
    public $class=__CLASS__;
    
    public function apiMain(Param $param)
    {
        $uid=User::getSignInUserId();
        if (!User::hasPermision($uid,'upload')) return new \api\Error('noPermition','no Permision');
        // 取第一个文件
        $file=array_shift($_FILES);
        $state=intval ( isset($_POST['state'])?$_POST['state']:MUpload::STATE_PUBLISH );
        $type=pathinfo($file['name'], PATHINFO_EXTENSION);
        $name=$file['name'];
        $hash=md5_file($file['tmp_name']);

        $path=SITE_RESOURCE.'/uploads';
        Storage::mkdirs($path);
        if (move_uploaded_file($file['tmp_name'],$file=$path.'/'.$hash))
        {
            $size=filesize($file);
            $id=MUpload::create($uid,$name,$size,$type,$hash,0,$state);
        }
        return ['id'=>$id];
    }
}