<?php
namespace user;
use UManager;
use Page;

class Index
{
    public function main()
    {
        if (UManager::has_signin()){
            echo '用户中心';
        }
        else{
            // (new SignIn())->main();
            Page::redirect('/user/SignIn');
        }
    }
}
