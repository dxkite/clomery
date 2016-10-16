<?php
namespace user;
use DB_User;
use Page;
use Core\Value;

class Index
{
    public function main()
    {
        if ($info=DB_User::hasSignin()){
            import('Site.functions');
            \Site\page_common_set();
            Page::getController()->noCache();
            if (DB_User::getInfo($info['uid'])){
                $exinfo=DB_User::getInfo($info['uid']);
            }
            else{
                DB_User::setDefaulInfo($info['uid'],43,'hhahhahh');
                $exinfo=DB_User::getInfo($info['uid']);
            }
            $info=array_merge($info,$exinfo);
            Page::use('user/index');
            Page::set('user_info',new Value($info));
            Page::set('signin_list',DB_User::getSigninLogs($info['uid']));
        }
        else{
            // (new SignIn())->main();
             Page::redirect('/user/SignIn');
        }
    }
}
