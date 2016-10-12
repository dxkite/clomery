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
            import('Site.functions');
            \Site\page_common_set();
            Page::getController()->noCache();
            if (UManager::getInfo($info['uid'])){
                $exinfo=UManager::getInfo($info['uid']);
            }
            else{
                UManager::setDefaulInfo($info['uid'],43,'hhahhahh');
                $exinfo=UManager::getInfo($info['uid']);
            }
            $info=array_merge($info,$exinfo);
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
