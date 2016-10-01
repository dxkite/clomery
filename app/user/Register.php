<?php
    import('Site.functions');
    Site\page_common_set();
    Page::getController();
    Page::use('user/register');