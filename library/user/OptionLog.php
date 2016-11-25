<?php
namespace user; 

use archive\Archive;

class OptionLog implements Arichive {
    protected static $_fields=['oid','uid','name','sketch','ip','time'];
    /**
     * 日志ID 
     * @var  int 
     */
    protected $oid;
    /**
     * 使用的用户 
     * @var  int 
     */
    protected $uid;
    /**
     * 操作名 
     * @var  string 
     */
    protected $name;
    /**
     * 操作附加描述 
     * @var  string 
     */
    protected $sketch;
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
     * @return  OptionLog   
     */
    public function setOid(int $oid) {
        $this->oid=$oid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getOid() : int {
        return $this->oid;
    }


    /**
     * @return  OptionLog   
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
     * @return  OptionLog   
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
     * @return  OptionLog   
     */
    public function setSketch(string $sketch) {
        $this->sketch=$sketch;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getSketch() : string {
        return $this->sketch;
    }


    /**
     * @return  OptionLog   
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
     * @return  OptionLog   
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