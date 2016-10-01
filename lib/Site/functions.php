<?php
namespace Site;
use Page;
function page_common_set()
{
    Page::set('copyright', 'mongci.cn');
    NavOp::init();
    $nav=NavOp::getNavs();
    Page::set('head_index_nav', $nav);
}