<?php
namespace cn\atd3\response\article;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\visitor\Context;
use cn\atd3\article\proxyobject\ArticleProxy;
use cn\atd3\proxy\Proxy;

class CoverResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $id=$context->getRequest()->get()->id(0);
        if ($id==0) {
            echo 'empty';
        } else {
            $file=(new Proxy(new ArticleProxy($context)))->getCover($id);
            if ($file) {
                return $this->fileContent($file->getPath(), $file->getName(), $file->getType());
            } else {
                echo 'empty';
            }
        }
    }
    
    public function fileContent(string $path, string $filename=null, string $type=null)
    {
        $content=file_get_contents($path);
        $hash   = md5($content);
        $size   = strlen($content);
        if (!$this->_etag($hash)) {
            self::setHeader('Content-Type:'.$type);
            self::setHeader('Content-Length:'.$size);
            echo $content;
        }
    }
}
