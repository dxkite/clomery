<?php
namespace user;
use UManager;
use Page;

class Index
{
    public function main()
    {
        if ($info=UManager::hasSignin()){
            Page::use('user/index');
            Page::set('user_name',$info['name']);
            Page::set('signin_list',UManager::getSigninLogs($info['uid']));
        }
        else{
            // (new SignIn())->main();
            Page::redirect('/user/SignIn');
        }
    }
}
