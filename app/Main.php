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
        View::set('title', '管理页面 - 三人行，必有我师焉。');
        $nav=NavOp::getNavs();
        var_dump(Options::getOptions());
        View::set('head_index', $nav);
        View::set('copyright', 'atd3.cn');
    }
    public function article(int $id=0)
    {
        //  var_dump($id);
        View::set('title', '文章- '.$id.' - 三人行，必有我师焉。');
    }
}
