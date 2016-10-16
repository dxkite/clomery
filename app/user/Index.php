<?php
namespace user;
use UserManager;
use Page;
use Core\Value;

class Index
{
    public function main()
    {
        if ($info=UserManager::hasSignin()){
            import('Site.functions');
            \Site\page_common_set();
            Page::getController()->noCache();
            if (UserManager::getInfo($info['uid'])){
                $exinfo=UserManager::getInfo($info['uid']);
            }
            else{
                UserManager::setDefaulInfo($info['uid'],43,'hhahhahh');
                $exinfo=UserManager::getInfo($info['uid']);
            }
            $info=array_merge($info,$exinfo);
            Page::use('user/index');
            Page::set('user_info',new Value($info));
            Page::set('signin_list',UserManager::getSigninLogs($info['uid']));
        }
        else{
            // (new SignIn())->main();
             Page::redirect('/user/SignIn');
        }
    }
}
