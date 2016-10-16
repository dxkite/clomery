<?php
namespace Site;

use Page;
use DB_Navigation;
use DB_User;
use Session;
use Core\Value;

function page_common_set()
{
    Page::global('_Op', new Options);
    // Page::global('_Env', new Env);
    DB_Navigation::init();
    $nav=DB_Navigation::getNavs();
    $user=DB_User::hasSignin();

    if ($user){
        Page::set('has_signin', true);
        Page::set('user_info', new Value($user) );
    }
    else{
        Page::set('has_signin', false);
    }
    
    Page::set('head_index_nav', $nav);
}
