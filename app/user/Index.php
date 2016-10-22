<?php
namespace user;
use Common_User;
use Page;
use Core\Value;

class Index
{
    public function main()
    {
        if ($info=Common_User::hasSignin()){
            import('Site.functions');
            \Site\page_common_set();
            Page::getController()->noCache();
            if (Common_User::getInfo($info['uid'])){
                $exinfo=Common_User::getInfo($info['uid']);
            }
            else{
                Common_User::setDefaulInfo($info['uid'],43,'hhahhahh');
                $exinfo=Common_User::getInfo($info['uid']);
            }
            
            $info=array_merge($info,$exinfo);
            Page::use('user/index');
            Page::set('user_info',new Value($info));
            Page::set('signin_list',Common_User::getSigninLogs($info['uid']));
        }
        else{
            // (new SignIn())->main();
             Page::redirect('/user/SignIn');
        }
    }
}
