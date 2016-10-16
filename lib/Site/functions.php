<?php
namespace Site;

use Page;
use Common_Navigation;
use Common_User;
use Session;
use Core\Value;
use Site_Options;

function page_common_set()
{
    Page::global('_Op', new Site_Options);
    // Page::global('_Env', new Env);
    Common_Navigation::init();
    $nav=Common_Navigation::getNavs();
    $user=Common_User::hasSignin();

    if ($user){
        Page::set('has_signin', true);
        Page::set('user_info', new Value($user) );
    }
    else{
        Page::set('has_signin', false);
    }
    
    Page::set('head_index_nav', $nav);
}
