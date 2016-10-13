<?php
namespace Site;

use Page;
use Env;
use UManager;
use Session;
use Core\Value;

function page_common_set()
{
    Page::global('_Op', new Options);
    Page::global('_Env', new Env);
    NavOp::init();
    $nav=NavOp::getNavs();
    $user=UManager::hasSignin();

    if ($user){
        Page::set('has_signin', true);
        Page::set('user_info', new Value($user) );
    }
    
    Page::set('head_index_nav', $nav);
}
