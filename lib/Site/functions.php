<?php
namespace Site;

use Page;
use Env;
use UManager;
use Session;

function page_common_set()
{
    Page::global('_Op', new Options);
    Page::global('_Env', new Env);
    NavOp::init();
    $nav=NavOp::getNavs();
    Page::set('has_signin', Session::get('signin', false));
    Page::set('signin_user', Session::get('user_name', 'No User'));
    Page::set('head_index_nav', $nav);
}
