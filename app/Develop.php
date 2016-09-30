<?php
use Site\NavOp;
use Site\Options;
class Develop
{

    public function main(string $name)
    {
        NavOp::init();
        View::set('title', '管理页面 - 三人行，必有我师焉。');
         $nav=NavOp::getNavs();
         $index='article|books|question|test|notes';
         $atr=explode('|',$index);
         $nav[array_search($name,$atr)+1]['select']=true;
         View::set('head_index', $nav);
         View::set('copyright', 'mongci.cn');
    }
}