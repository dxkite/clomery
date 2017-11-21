<?php
namespace cn\atd3\user;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\visitor\Permission;

class Install {
    public static function create(){
        $permissions=(new Permission)->getSystemPermissions();
        $user=new UserDAO;
        // var_dump($user->export(TEMP_DIR.'/database1.txt'));
        $user->createTable();
        // var_dump($user->import(TEMP_DIR.'/database.txt'));
        // 创建管理员 邮箱 密码
        $user->add('dxkite','dxkite@qq.com','#xk:tew0rd',1);
        // 创建分组
        $group=new GroupDAO;
        $group->createTable();
        $group->insert(['id'=>1,'name'=>'超级管理员','sort'=>0,'permissions'=> serialize($permissions)]);
    }
}