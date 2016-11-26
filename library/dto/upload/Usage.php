<?php

namespace dto\upload; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class Usage extends Archive {
    protected static $_fields=['rid','fid','uid','name','type','time','publish'];
    // 是否为可用字段
    protected function _isField($name){
        return in_array($name,self::$_fields);
    }
    public function getTableName():string
    {
        return 'upload_usage';
    }

}

/**
* DTA FILE:
; 文件使用记录
rid bigint(20) primary auto comment="文件ID"
fid bigint(20) key comment="文件资源"
uid bigint(20) key comment="使用用户"
name varchar(80) comment="文件名" 
type varchar(10) key comment="扩展名"
time int(11) comment="时间"
publish int(1) default=1 comment="是否公开"
*/