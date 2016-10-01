<?php
use Site\NavOp;
use Site\Options;
import('Site.functions');
class Develop
{
    public function main(string $name)
    {
        Page::set('site_title', '网页开发中');
        $index='article|books|question|test|notes';
        $atr=explode('|', $index);
        Site\page_common_set();
        Page::set('head_index_nav_select',array_search($name, $atr)+1);
    }
}
