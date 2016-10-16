<?php

class Authority
{
    public static $uid=0;
    public function setUid(int $uid)
    {
        sefl::$uid=$uid;
    }
    /**
    * 使用别人的名义
    */
    public static function su2Other() :bool
    {
        $cmd='SELECT `U_su` FROM `atd_users` JOIN`atd_groups` ON `atd_groups`.`gid`=`atd_users`.`gid` WHERE `atd_users`.`uid`= :uid LIMIT 1;';
        if ($q=(new Query($cmd, ['uid'=>self::$uid]))->fetch()) {
            return $q['U_su']==='Y';
        }
        return false;
    }
}
