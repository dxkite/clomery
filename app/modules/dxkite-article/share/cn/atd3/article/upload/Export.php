<?php
namespace cn\atd3\article\upload;
use cn\atd3\proxy\ProxyObject;
use cn\atd3\upload\File;
use suda\tool\ZipHelper;

class Export  extends ProxyObject {
    public function upload(File $article) {
        $path=TEMP_DIR.'/article_temp';
        $path=TEMP_DIR.'/article_temp/'.md5($article->getPath());
        ZipHelper::unzip($article->getPath(),$path);
        return $article;
    }

    public function test(){
        HtmlFilter::filter();
    }
}