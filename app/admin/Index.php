<?php
namespace admin;

use System;
use Page;
use Request;
use Event;
use Core\Caller;
use Core\Value;

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
                $options[]=new Value(['title'=>'管理网站','href'=>Page::url('admin',['path'=>'Site'])]);
                Page::set('options',$options);
                Page::use('admin/index');
            } else {
                echo 'no perimission';
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
}
