<?php
namespace api;

use model\Upload as MUpload;
use Storage;
use Request;
use Page;
use api\Error as  ApiError;

class Upload
{
    public function upload(int $uid, int  $state)
    {
        // 取第一个文件
        $file=array_shift($_FILES);
        $type=pathinfo($file['name'], PATHINFO_EXTENSION);
        $name=$file['name'];
        $hash=md5_file($file['tmp_name']);
        $path=SITE_RESOURCE.'/uploads';
        Storage::mkdirs($path);
        if (move_uploaded_file($file['tmp_name'], $file=$path.'/'.$hash)) {
            $size=filesize($file);
            $id=\model\Upload::create($uid, $name, $size, $type, $hash, 0, $state);
        }
        return ['id'=>$id];
    }
    
    public function get($id)
    {
        $file=MUpload::getWhen($id, MUpload::STATE_PUBLISH);
        $path=SITE_RESOURCE.'/uploads/'.$file['hash'];
        if (Storage::exist($path)) {
            Page::setType($file['type']);
            if (isset($_GET['download'])) {
                header('Content-Disposition:attachment;filename='.$file['name']);
            }
            echo Storage::get($path);
        } else {
            Page::json();
            return new ApiError('uploadNoFind', 'upload id : '.$id);
        }
    }

    public function mapperGet($request)
    {
        return self::get($request->get()->id(0));
    }
}
