<?php
namespace cn\atd3\response\user;
use cn\atd3\api\response\OnCallableResponse;
use cn\atd3\oauth\baidu\BaiduExport;

class BaiduResponse extends OnCallableResponse
{
    public function getExportMethods($class=NULL)
    {
        return parent::getExportMethods(new BaiduExport($this->getContext()));
    }
}
