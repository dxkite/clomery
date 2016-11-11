<?php
namespace admin;

use Page;
use Core\Value;
use Request;
use Common_Navigation;
use Common_User;

class User extends \Page_Admin
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
        } elseif (Request::get()->edit) {
            self::edit(Request::get()->edit);
        } elseif (Request::get()->send_mail) {
            Common_User::sendMail(Request::get()->send_mail);
            header('Location:'.$_SERVER['PHP_SELF'].'?edit='.Request::get()->send_mail(0));
        }
        // 列表
        else {
            if (Request::post()->users) {
                switch (Request::post()->do) {
                    case 'sendmail': self::sendmail(Request::post()->users); break;
                    case 'delete':self::delete(Request::post()->users);break;
                }
                header('Location:'.$_SERVER['PHP_SELF']);
            } else {
                self::listUser();
            }
        }
    }

    public function edit(int $user)
    {
        Page::set('title', '编辑用户');
        if (Request::hasPost()) {
            $post=Request::post();
            if (isset($post->delete)) {
                Common_User::delete($user);
                header('Location:'.$_SERVER['PHP_SELF']);
            } else {
                var_dump(Common_User::modify($user, Request::post()->name, Request::post()->group, Request::post()->email, Request::post()->email_verify, Request::post()->status));
                if ($passwd=Request::post()->passwd) {
                    Common_User::changePasswd($user, $passwd);
                }
                header('Location:'.$_SERVER['PHP_SELF'].'?edit='.$user);
            }
        }
        Page::set('user', new Value(Common_User::getBaseInfo($user)));
        $groups=Common_User::getGroups();
        $groups[]=['gid'=>0,'gname'=>'未分组'];
        Page::set('groups', $groups);
        Page::insertCallback('Admin-Content', function () {
            Page::set('navs', Common_Navigation::getNavsets());
            Page::render('admin/user-edit');
        });
    }

    public function listUser()
    {
        Page::set('title', '用户管理');
        $page_count=10;
        $count= Common_User::count();
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

    private function sendmail(array $users)
    {
        foreach ($users as $uid => $send) {
            if (!Common_User::emailVerified($uid) && $send == 'on') {
                Common_User::sendMail($uid);
            }
        }
    }

    private function delete(array $users)
    {
        foreach ($users as $uid => $send) {
            if ($send=='on') {
                Common_User::delete($uid);
            }
        }
    }
}
