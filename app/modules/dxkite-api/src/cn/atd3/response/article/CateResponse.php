<?php
namespace cn\atd3\response\article;
use cn\atd3\api\response\OnCallableResponse;
use cn\atd3\article\proxyobject\CategoryProxy;


class CateResponse extends OnCallableResponse
{
    public function getExportMethods($class=NULL)
    {
        return parent::getExportMethods(new CategoryProxy($this->getContext()));
    }
}
