<?php
namespace admin;


use Page;
use Core\Value;
use Request;
use Common_Navigation;

class Navigation extends \Admin_Autoentrance
{
    
    public function run()
    {
        $mod=Request::get()->mod;
        Page::set('id', Request::get()->id);
        Page::set('mod', $mod);
        switch ($mod) {
            case 'create':
            if (Request::post()->nav_create) {
                Common_Navigation::create(Request::post()->nav_create);
                header('Location:'.$_SERVER['PHP_SELF']);
            } else {
                Page::set('title', '创建新导航');
                Page::insertCallback('Admin-Content', function () {
                    Page::render('admin/nav-create');
                });
            }
            break;
            case 'modify':
                Page::set('title', '修改导航 - '.Request::get()->id);
                if (Request::post()->nav_set) {
                    Common_Navigation::update(Request::get()->id, Request::post()->nav_set);
                    header('Location:'.$_SERVER['PHP_SELF']);
                } else {
                    $nav=Common_Navigation::getNavById(Request::get()->id);
                    Page::set('nav', $nav);
                    Page::insertCallback('Admin-Content', function () {
                        Page::render('admin/nav-modify');
                    });
                }
            break;
            case 'delete':
                Common_Navigation::delete(Request::get()->id);
                header('Location:'.$_SERVER['PHP_SELF']);
            break;
            case 'sort':
            default:
            if (Request::post()->nav_sort) {
                foreach (Request::post()->nav_sort as $id => $sort) {
                    Common_Navigation::sort($id, $sort);
                }
                header('Location:'.$_SERVER['PHP_SELF']);
            }
            Page::set('title', '导航设置');
            Page::insertCallback('Admin-Content', function () {
                Page::set('navs', Common_Navigation::getNavsets());
                Page::render('admin/nav-sort');
            });
        }
    }
}