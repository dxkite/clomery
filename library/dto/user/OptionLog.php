<?php

namespace dto\user; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class OptionLog extends Archive {
    protected static $_fields=['oid','uid','name','sketch','ip','time'];
    // 是否为可用字段
    protected function _isField($name){
        return in_array($name,self::$_fields);
    }
    public function getTableName():string
    {
        return 'user_option_log';
    }

}

/**
* DTA FILE:
; 操作日志
oid bigint(20) primary auto comment="日志ID"
uid bigint(20) key comment="使用的用户"
name varchar(80) key comment="操作名"
sketch varchar(255) comment="操作附加描述"
ip varchar(32) comment="使用令牌的ID"
time int(11) comment="使用的时间"
*/