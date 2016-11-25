<?php

namespace user; 

use archive\Archive;
use archive\Condition;
use archive\Statement;

class User implements Archive {
    protected static $_fields=['uid','name','password','groupid'];
    /**
     * 用户ID 
     * @var  int 
     */
    protected $uid;
    /**
     * 用户名 
     * @var  string 
     */
    protected $name;
    /**
     * 密码HASH 
     * @var  string 
     */
    protected $password;
    /**
     * 分组ID 
     * @var  int 
     */
    protected $groupid;



    /**
     * @return  User   
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
     * @return  User   
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
     * @return  User   
     */
    public function setPassword(string $password) {
        $this->password=$password;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getPassword() : string {
        return $this->password;
    }


    /**
     * @return  User   
     */
    public function setGroupid(int $groupid) {
        $this->groupid=$groupid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getGroupid() : int {
        return $this->groupid;
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
        return 'CREATE TABLE `user` (
	`uid` bigint(20) NOT NULL  AUTO_INCREMENT COMMENT \'用户ID\',
	`name` varchar(13) NOT NULL   COMMENT \'用户名\',
	`password` varchar(60) NOT NULL   COMMENT \'密码HASH\',
	`groupid` bigint(20) NOT NULL DEFAULT \'0\'  COMMENT \'分组ID\',
	PRIMARY KEY (`uid`),
	UNIQUE KEY `name` (`name`),
	KEY `groupid` (`groupid`)
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
		$sql='INSERT INTO `user` ('.trim($names,',').') VALUES ('.trim($bind,',').');';
		return new Statement($sql,$param);
    }
    function sqlRetrieve(Condition $condition):Statement{
		
	}
    function sqlUpdate():Statement{}
    function sqlDelete():Statement{}
}

/**
* DTA FILE:
; 用户表
uid bigint(20) auto comment="用户ID" primary 
name varchar(13) unique comment="用户名"
password varchar(60) comment="密码HASH"
groupid bigint(20) key  default=0 comment="分组ID"
*/