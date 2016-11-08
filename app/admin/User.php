<?php
namespace admin;

use Page;
use Core\Value;
use Request;
use Common_Navigation;

class User extends \Admin_Autoentrance
{
    public function run()
    {
        Page::set('title', '用户管理');
        $page_count=20;
        $count= \Common_User::numbers();
        $max=ceil($count/$page_count);

        $page=Request::get()->page(1)>$max?$max:Request::get()->page(1);
        $users= \Common_User::listUser($page-1,$page_count);
        

        $pages=range($page-5>1?$page-5:1,$page+5>$max?$max:$page+5);
       
        foreach ($users as $key=>$user) {
            $user['group']=\Common_User::gid2name($user['gid']);
            $users[$key]=new Value($user);
        }
        Page::set('page_current',$page);
        Page::set('pages',$pages);
        Page::set('users', $users);
        Page::insertCallback('Admin-Content', function () {
            Page::set('navs', Common_Navigation::getNavsets());
            Page::render('admin/user-list');
        });
    }
}
