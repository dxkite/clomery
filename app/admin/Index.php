<?php

if (System::user()->hasSignin) {
    echo 'signin';
    var_dump(System::user()->avatar);
    var_dump(Request::get()->runner);
    if (System::user()->permission->editSite) {
        echo 'have perimission';
    } else {
        echo 'no perimission';
    }
} else {
    Page::redirect('/user/SignIn');
}
