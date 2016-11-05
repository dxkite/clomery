<?php
namespace admin;

use System;
use Page;
use Request;
use Event;
use Core\Caller;

class Index
{
    public function entrance($addon='')
    {

    }
    
    public function main()
    {
        if (System::user()->hasSignin) {
            if (System::user()->permission->editSite) {
                // Page::set('admin_entrance');
                Page::use('admin/index');
            } else {
                echo 'no perimission';
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
}
