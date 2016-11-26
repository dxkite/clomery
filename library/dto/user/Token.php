<?php

namespace dto\user; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class Token extends Archive {
    protected static $_fields=['tid','uid','token','name','ip','time','expire','value'];
    // 是否为可用字段
    protected function _isField($name){
        return in_array($name,self::$_fields);
    }
    public function getTableName():string
    {
        return 'user_token';
    }

}

/**
* DTA FILE:
; 令牌表
tid bigint(20) auto comment="令牌ID" primary 
uid bigint(20) key comment="使用的用户"
token varchar(32) key comment="令牌"
name varchar(80) key comment="命令名"
ip varchar(32) comment="使用令牌的ID"
time int(11) comment="使用的时间"
expire int(11) comment="过期时间"
value varchar(255) comment="附加值"
*/