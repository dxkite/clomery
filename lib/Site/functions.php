<?php
namespace Site;

use Page;
use Env;
use DB_User;
use Session;
use Core\Value;

function page_common_set()
{
    Page::global('_Op', new Options);
    Page::global('_Env', new Env);
    NavOp::init();
    $nav=NavOp::getNavs();
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
