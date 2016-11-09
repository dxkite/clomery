<?php
namespace admin;

use Page;
use Core\Value;
use Request;
use Common_Navigation;
use Common_User;
class User extends \Admin_Autoentrance
{
    public function run()
    {
        // 封禁
        if (Request::get()->freeze) {
            Common_User::setStatu(Request::get()->freeze);
            header('Location:'.$_SERVER['PHP_SELF']);
        }
        // 解封 
        elseif (Request::get()->active) {
            Common_User::setStatu(Request::get()->active, 0);
            header('Location:'.$_SERVER['PHP_SELF']);
        }
        elseif (Request::get()->edit)
        {
            self::edit(Request::get()->edit);
        }
        // 列表 
        else {
            self::listUser();
        }
    }
    public function edit(int $user)
    {
        Page::set('title', '编辑用户');
        Page::set('user',new Value(Common_User::getBaseInfo($user)));
        Page::set('groups',Common_User::getGroups());
        Page::insertCallback('Admin-Content', function () {
            Page::set('navs', Common_Navigation::getNavsets());
            Page::render('admin/user-edit');
        });
        var_dump(Request::post());
    }

    public function listUser()
    {
        Page::set('title', '用户管理');
        $page_count=10;
        $count= Common_User::numbers();
        $max=ceil($count/$page_count);
        $page=Request::get()->page(1)>$max?$max:Request::get()->page(1);
        $users= Common_User::listUser($page-1, $page_count);
        $pages=range($page-5>1?$page-5:1, $page+5>$max?$max:$page+5);
       
        foreach ($users as $key=>$user) {
            $user['group']=Common_User::gid2name($user['gid']);
            $users[$key]=new Value($user);
        }
        Page::set('page_current', $page);
        Page::set('pages', $pages);
        Page::set('users', $users);
        Page::insertCallback('Admin-Content', function () {
            Page::set('navs', Common_Navigation::getNavsets());
            Page::render('admin/user-list');
        });
    }
}
