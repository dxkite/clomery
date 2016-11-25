<?php
namespace user; 
class User {
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
}