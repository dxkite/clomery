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
            return (new ArticleArchive($article,$type))->save($this->getUserId(),$status);
        }
        return false;
    }

    public function html() {
        $config = \HTMLPurifier_Config::createDefault();

        // configuration goes here:
        $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
        
        $purifier = new \HTMLPurifier($config);
        
        // untrusted input HTML
        $html = '<b>Simple and short';
        
        $pure_html = $purifier->purify($html);
        
       return $pure_html;
 
    }
}