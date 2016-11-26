<?php

namespace dto\upload; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class Upload extends Archive {
    protected static $_fields=['fid','type','hash','ref'];
    // 是否为可用字段
    protected function _isField($name){
        return in_array($name,self::$_fields);
    }
    public function getTableName():string
    {
        return 'upload';
    }

}

/**
* DTA FILE:
; 上传的文件
fid bigint(20) primary  auto comment="文件ID"
type varchar(10) key comment="扩展名"
hash varchar(32) key comment="MD5哈希"
ref  int(11) comment="引用计数"  
*/