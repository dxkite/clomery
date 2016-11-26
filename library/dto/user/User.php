<?php

namespace dto\user; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class User extends Archive {
    protected static $_fields=['uid','name','email','password','groupid'];
    // 是否为可用字段
    protected function _isField($name){
        return in_array($name,self::$_fields);
    }
    public function getTableName():string
    {
        return 'user';
    }

}

/**
* DTA FILE:
; 用户表
uid bigint(20) auto comment="用户ID" primary 
name varchar(13) unique comment="用户名"
email varchar(50) unique comment="邮箱"
password varchar(60) comment="密码HASH"
groupid bigint(20) key  default=0 comment="分组ID"
*/