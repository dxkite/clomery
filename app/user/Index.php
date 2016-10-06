<?php
namespace user;
use UManager;

class Index
{
    public function main()
    {
        if (UManager::has_signin()){
            echo '用户中心';
        }
        else{
            (new SignIn())->main();
        }
    }
}
