<?php

namespace user; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class OptionLog implements Archive {
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
    function getFeilds():array
    {
        return self::$_fields;
    }
    function getAvailableFields():array
    {
        $available=[];
        foreach (self::$_fields as $name){
            if (isset($this->{$name})){
                $available[]=$name;
            }
        }
        return $available;
    }
    function tableCreator():string{
        return 'CREATE TABLE `user_option_log` (
	`oid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT \'日志ID\',
	`uid` bigint(20) NOT NULL   COMMENT \'使用的用户\',
	`name` varchar(80) NOT NULL   COMMENT \'操作名\',
	`sketch` varchar(255) NOT NULL   COMMENT \'操作附加描述\',
	`ip` varchar(32) NOT NULL   COMMENT \'使用令牌的ID\',
	`time` int(11) NOT NULL   COMMENT \'使用的时间\',
	PRIMARY KEY (`oid`),
	KEY `uid` (`uid`),
	KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
    }
    function sqlCreate():Statement{
		$values=self::getAvailableFields();
		$param=[];
		$bind='';
		$names='';
		foreach ($values as $name)
		{
			$bind.=':'.$name.',';
			$names.='`'.$name.'`,';
			$param[$name]=$this->{$name};
		}
		$sql='INSERT INTO `user_option_log` ('.trim($names,',').') VALUES ('.trim($bind,',').');';
		return new Statement($sql,$param);
    }
    function sqlRetrieve(Condition $condition):Statement{
		
	}
    function sqlUpdate():Statement{}
    function sqlDelete():Statement{}
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