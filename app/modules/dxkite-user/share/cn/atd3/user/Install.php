<?php
namespace cn\atd3\user;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\dao\GroupDAO;
class Install {
    public static function create(){
        $permissions='a:4:{i:0;s:11:"admin:pages";i:1;s:13:"admin:website";i:2;s:10:"admin:user";i:3;s:11:"admin:group";}';
        $user=new UserDAO;
        // var_dump($user->export(TEMP_DIR.'/database1.txt'));
        $user->createTable();
        // var_dump($user->import(TEMP_DIR.'/database.txt'));
        // 创建管理员 邮箱 密码
        $user->add('dxkite','dxkite@qq.com','dxkite',1);
        // 创建分组
        $group=new GroupDAO;
        $group->createTable();
        $group->insert(['id'=>1,'name'=>'超级管理员','sort'=>0,'permissions'=>$permissions]);
    }
}