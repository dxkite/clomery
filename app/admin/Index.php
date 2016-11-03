<?php
namespace admin;

use System;
use Page;
use Request;
use Event;
use Core\Caller;

class Index
{
    public function insertDisplayer()
    {
        Page::insertCallback('Display:AdminPage_Menu', [$this, 'displayMenu']);
        Page::insertCallback('Display:AdminPage_Content',[$this, 'displayContent']);
    }

    public function displayMenu()
    {
        echo '<h1>菜单'.$_SERVER['PHP_SELF'].'?admin=site</h1>';
    }

    public function displayContent()
    {
        echo '<h2>内容</h2>';
        return false;
    }
    
    public function main()
    {
        if (System::user()->hasSignin) {
            echo 'signin';
            var_dump(System::user()->avatar);
            var_dump(Request::get()->admin);
            if (System::user()->permission->editSite) {
                self::insertDisplayer();
                Page::insertSelect('Display:AdminPage_Content',ucfirst(Request::get()->admin));
                echo 'have perimission';
                Page::use('admin/index');
            } else {
                echo 'no perimission';
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
}
