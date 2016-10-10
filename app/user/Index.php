<?php
namespace user;
use UManager;
use Page;
use Core\Value;

class Index
{
    public function main()
    {
        if ($info=UManager::hasSignin()){
            Page::getController()->noCache();
            Page::use('user/index');
            Page::set('user_info',new Value($info));
            Page::set('signin_list',UManager::getSigninLogs($info['uid']));
        }
        else{
            // (new SignIn())->main();
             Page::redirect('/user/SignIn');
        }
    }
}
