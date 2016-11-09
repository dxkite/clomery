<?php
namespace user;
use Page;
use Common_User;
use PageUrl;

class View
{
    public function main($uid)
    {
       
       $info=Common_User::getPublicInfo((int)$uid);
       if (isset($info['uid'])){
            $info['avatar_url']=PageUrl::avatar($info['name']);
            $user=new \Core\Value($info);
            Page::set('user', $user);
            Page::set('title', $user->name.' - 的主页');
            Page::use('/user/user');
       }
    }

    public function avatar(string $name=''){
        $info=Common_User::getPublicInfoByName($name);
        if (isset($info['avatar'])){
            \Upload::outputPublic($info['avatar']);
        }
        else{
            var_dump($info);
        }
    }
}
