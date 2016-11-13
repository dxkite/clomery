<?php
if (Common_User::hasSignin()) {
     Page::redirect('/user');
} else {
    Page::global('_Op', new Site_Options);
    Page::getController()->noCache();
    Page::use('user/signup');
}
