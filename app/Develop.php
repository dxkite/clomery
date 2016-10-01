<?php
use Site\NavOp;
use Site\Options;

class Develop
{
    public function main(string $name)
    {
        NavOp::init();
        Page::set('title', '管理页面 - 三人行，必有我师焉。');
        $nav=NavOp::getNavs();
        $index='article|books|question|test|notes';
        $atr=explode('|', $index);
        $nav[array_search($name, $atr)+1]['select']=true;
        Page::set('head_index', $nav);
        Page::set('copyright', 'mongci.cn');
    }
}
