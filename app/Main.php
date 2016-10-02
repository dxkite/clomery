<?php
use Site\NavOp;
use Site\Options;
import('Site.functions');

class Main
{
    public function __construct()
    {
        NavOp::init();
    }
    public function main()
    {
        Site\page_common_set();
        Page::set('site_title',Options::getOptions()['site_title']);
        Page::set('head_index_nav_select', 0 );
    }
    public function article(int $id=0)
    {
        //  var_dump($id);
        Page::set('title', '文章- '.$id.' - 三人行，必有我师焉。');
    }
}
