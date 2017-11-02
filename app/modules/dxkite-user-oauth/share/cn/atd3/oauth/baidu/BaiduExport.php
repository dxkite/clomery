<?php
namespace cn\atd3\oauth\baidu;

use cn\atd3\proxy\ProxyObject;

class BaiduExport extends ProxyObject
{
    public function getAuthUrl()
    {
        return  Manager::getAuthUrl();
    }

    public function getUserInfo() {
        $userId=$this->context->getVisitor()->getId();
        return table('baidu_user')->select(['user','uid','uname','portrait'], ['user'=>$userId])->fetch();
    }
}
