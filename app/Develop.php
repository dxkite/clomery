<?php

class Develop  extends Page_Main
{
    public function run(string $name)
    {
        Page::set('title', '网页开发中');
        Page::set('head_index_nav_select',2);
    }
}
