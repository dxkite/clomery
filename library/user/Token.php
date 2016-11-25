<?php
namespace user; 
class Token {
    /**
     * 令牌ID 
     * @var  int 
     */
    protected $tid;
    /**
     * 使用的用户 
     * @var  int 
     */
    protected $uid;
    /**
     * 命令名 
     * @var  string 
     */
    protected $name;
    /**
     * 使用令牌的ID 
     * @var  string 
     */
    protected $ip;
    /**
     * 使用的时间 
     * @var  int 
     */
    protected $time;
    /**
     * 过期时间 
     * @var  int 
     */
    protected $expire;
    /**
     * 附加值 
     * @var  string 
     */
    protected $value;



    /**
     * @return  Token   
     */
    public function setTid(int $tid) {
        $this->tid=$tid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getTid() : int {
        return $this->tid;
    }


    /**
     * @return  Token   
     */
    public function setUid(int $uid) {
        $this->uid=$uid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getUid() : int {
        return $this->uid;
    }


    /**
     * @return  Token   
     */
    public function setName(string $name) {
        $this->name=$name;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getName() : string {
        return $this->name;
    }


    /**
     * @return  Token   
     */
    public function setIp(string $ip) {
        $this->ip=$ip;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getIp() : string {
        return $this->ip;
    }


    /**
     * @return  Token   
     */
    public function setTime(int $time) {
        $this->time=$time;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getTime() : int {
        return $this->time;
    }


    /**
     * @return  Token   
     */
    public function setExpire(int $expire) {
        $this->expire=$expire;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getExpire() : int {
        return $this->expire;
    }


    /**
     * @return  Token   
     */
    public function setValue(string $value) {
        $this->value=$value;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getValue() : string {
        return $this->value;
    }
}

/**
* DTA FILE:
; 令牌表
tid bigint(20) auto comment="令牌ID" primary 
uid bigint(20) key comment="使用的用户"
name varchar(80) key comment="命令名"
ip varchar(32) comment="使用令牌的ID"
time int(11) comment="使用的时间"
expire int(11) comment="过期时间"
value varchar(255) comment="附加值"
*/