<?php
if (UManager::has_signin()) {
    Page::redirect('/user');
} else {
    import('Site.functions');
    Site\page_common_set();
    Page::getController();
    Page::use('user/signup');
}
