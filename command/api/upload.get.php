<?php
namespace api;

use model\Upload as MUpload;
use Storage;
use Request;
use Page;
use api\Error as  ApiError;

class Upload
{
    public function beforeRun(){
        Page::setFlush(true);
    }
    public function main(Request $rq)
    {
        if ($id= $rq->get()->id(0)) {
            $file=MUpload::getWhen($id, MUpload::STATE_PUBLISH);
            $path=SITE_RESOURCE.'/uploads/'.$file['hash'];
            if (Storage::exist($path)) {
                Page::setType($file['type']);
                echo Storage::get($path);
            } else {
                Page::json();
                return new ApiError('uploadNoFind','upload id : '.$id);
            }
        }
    }
}
