<?php
namespace admin;

use System;
use Page;
use Request;
use Event;
use Core\Caller;

class Index
{
    public function insertDisplayer(string $page)
    {
        Page::insertCallback('Display:AdminPage_Menu', [$this, 'displayMenu']);
        var_dump('display'.$page.'Content');
        Page::insertCallback('Display:AdminPage_Content',[$this, 'display'.$page.'Content']);
    }

    public function displayMenu()
    {
        echo '<h1>菜单'.$_SERVER['PHP_SELF'].'?admin=site</h1>';
    }

    public function displaySiteContent()
    {
        echo '<h2>Site内容</h2>';
        return false;
    }
    
    public function main()
    {
        if (System::user()->hasSignin) {
            echo 'signin';
            var_dump(System::user()->avatar);
            var_dump(Request::get()->admin);
            if (System::user()->permission->editSite) {
                self::insertDisplayer(ucfirst(Request::get()->admin));
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
