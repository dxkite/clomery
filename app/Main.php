<?php
use Site\NavOp;
use Site\Options;

class Main
{
    public function __construct()
    {
        NavOp::init();
    }
    public function main()
    {
        Page::set('title', ' 三人行，必有我师焉。');
        $nav=NavOp::getNavs();
        $nav[0]['select']=true;
        Page::set('head_index', $nav);
        Page::set('copyright', 'mongci.cn');
    }
    public function article(int $id=0)
    {
        //  var_dump($id);
        Page::set('title', '文章- '.$id.' - 三人行，必有我师焉。');
    }
}
