<?php

use Site\Options;
import('Site.functions');

class Main
{
    public function __construct()
    {
        Common_Navigation::init();
    }
    public function main()
    {
        Site\page_common_set();
        Page::set('title',Options::getOptions()['site_name']);
        Page::set('head_index_nav_select', 0 );
    }
}
