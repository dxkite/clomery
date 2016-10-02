<?php
namespace user;
use Page;
class SignIn{
    function main()
    {
         import('Site.functions');
         \Site\page_common_set();
        Page::getController();
        Page::use('user/signin');
    }
}
   