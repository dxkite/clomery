<?php
// 权限？？
class User_Permission
{
    public $permission=[];
    public $success=false;
    public $sort=0;
    public $name;
    public $gid=0;
    public $uid=0;

    public static $alias=['editSite'=>'E_Site'];

    public function __construct(int $uid)
    {
        $this->uid=$uid;
        // 获取组别
        self::id2group();
        $cmd='SELECT * FROM `atd_groups` WHERE `gid`= :gid  LIMIT 1;';
        if ($set=(new Query($cmd, ['gid'=>$this->gid]))->fetch()) {
            $this->name=$set['gname'];
            unset($set['gname']);
            unset($set['gid']);
            $this->sort=$set['sort'];
            unset($set['sort']);
            $this->permission=$set;
            $this->success=true;
        }
    }

    public function success()
    {
        return $this->success;
    }

    public function __get(string $name)
    {
        $name=isset(self::$alias[$name])?self::$alias[$name]:$name;
        if (isset($this->permission[$name])) {
            return $this->permission[$name]==='Y';
        }
        return false;
    }

    // 可能废弃的不想用（后续多组？？
    private function id2group()
    {
        $q='SELECT `gid` FROM `#{users}` WHERE `uid` = :uid LIMIT 1;';
        if ($sets=(new Query($q, ['uid'=>$this->uid]))->fetch()) {
            return $this->gid=$sets['gid'];
        }
        return 0;
    }
}
