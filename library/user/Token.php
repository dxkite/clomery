<?php
namespace user; 
class Token {
    /** bigint(20) */
    protected $tid;
    /** bigint(20) */
    protected $uid;
    /** varchar(80) */
    protected $name;
    /** varchar(32) */
    protected $ip;
    /** int(11) */
    protected $time;
    /** int(11) */
    protected $expire;
    /** varchar(255) */
    protected $value;

    public function setTid(int $tid){
        $this->tid=$tid;
        return $this;
    }
    public function setUid(int $uid){
        $this->uid=$uid;
        return $this;
    }
    public function setName(string $name){
        $this->name=$name;
        return $this;
    }
    public function setIp(string $ip){
        $this->ip=$ip;
        return $this;
    }
    public function setTime(int $time){
        $this->time=$time;
        return $this;
    }
    public function setExpire(int $expire){
        $this->expire=$expire;
        return $this;
    }
    public function setValue(string $value){
        $this->value=$value;
        return $this;
    }
}