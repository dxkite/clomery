<?php
namespace user; 
class User {
    /** bigint(20) */
    protected $uid;
    /** varchar(13) */
    protected $name;
    /** varchar(60) */
    protected $password;
    /** bigint(20) */
    protected $groupid;

    public function setUid(int $uid){
        $this->uid=$uid;
        return $this;
    }
    public function setName(string $name){
        $this->name=$name;
        return $this;
    }
    public function setPassword(string $password){
        $this->password=$password;
        return $this;
    }
    public function setGroupid(int $groupid){
        $this->groupid=$groupid;
        return $this;
    }
}