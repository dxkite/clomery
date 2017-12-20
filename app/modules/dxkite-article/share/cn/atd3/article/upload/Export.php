<?php
namespace cn\atd3\article\upload;
use cn\atd3\proxy\ProxyObject;
use cn\atd3\upload\File;
use suda\tool\ZipHelper;

class Export  extends ProxyObject {

    /**
    * 上传文件压缩包
    *
    * @param File $article
    * @param string $type
    * @param int $status
    * @return bool
    */
    public function upload(File $article,string $type,int $status) : bool
    {
        $type=strtolower($type);
        if (in_array($type,['xml'])) {
            return (new ArticleArchive($article,$type))->save($status);
        }
        return false;
    }
}