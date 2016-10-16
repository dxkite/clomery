<?php
if (UserManager::hasSignin()) {
    Page::redirect('/user');
} else {
    import('Site.functions');
    \Site\page_common_set();
     Page::getController()->noCache();
    Page::use('user/signin');
}
