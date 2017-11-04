<?php
namespace cn\atd3\oauth\baidu;

use cn\atd3\proxy\ProxyObject;

class BaiduExport extends ProxyObject
{
    public function getAuthUrl()
    {
        return  Manager::getAuthUrl();
    }

    public function getInfo() {
        $userId=$this->context->getVisitor()->getId();
        return table('baidu_user')->select(['user','uid','uname','portrait'], ['user'=>$userId])->fetch();
    }
    
    public function setEmail(string $email)
    {
        return table('user')->update(['email'=>$email],'id=:id and  email is null',['id'=>$this->context->getVisitor()->getId()]);
    }

    public function setName(string $name)
    {
        return table('user')->update(['name'=>$name],'id=:id name is null',['id'=>$this->context->getVisitor()->getId()]);
    }

    public function checkNameExist(string $name)
    {
        return table('user')->checkNameExists($name)?true:false;
    }
    
    public function checkEmailExist(string $name)
    {
        return table('user')->checkEmailExists($name)?true:false;
    }
}
