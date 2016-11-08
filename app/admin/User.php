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
        $users= \Common_User::listUser();
        
        foreach ($users as $key=>$user) {
            $user['group']=\Common_User::gid2name($user['gid']);
            $users[$key]=new Value($user);
        }

        Page::set('users', $users);
        Page::insertCallback('Admin-Content', function () {
            Page::set('navs', Common_Navigation::getNavsets());
            Page::render('admin/user-list');
        });
    }
}
