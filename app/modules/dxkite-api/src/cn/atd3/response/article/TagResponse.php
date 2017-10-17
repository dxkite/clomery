<?php
namespace cn\atd3\response\article;
use cn\atd3\api\response\OnCallableResponse;
use cn\atd3\article\proxyobject\TagProxy;
use cn\atd3\upload\File;

class TagResponse extends OnCallableResponse
{
    public function getExportMethods($class=NULL)
    {
        return parent::getExportMethods(new TagProxy($this->getContext()));
    }
}
