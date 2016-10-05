<?php
namespace user;
use Page;
use UManager;
class SignOut
{
    public function main()
    {
        Page::getController()->raw();
        UManager::signout();
       echo '退出登陆';
    }
}
