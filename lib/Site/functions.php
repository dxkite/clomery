<?php
namespace Site;
use Page;
use Env;

function page_common_set()
{
    Page::global('_Op',new Options);
    Page::global('_Env',new Env);
    Env::Options()->copyright='mongci.cn';
    NavOp::init();
    $nav=NavOp::getNavs();
    Page::set('head_index_nav', $nav);
}