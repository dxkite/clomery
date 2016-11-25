<?php
namespace user; 
class Permision {
    /**
     * 权限ID 
     * @var  int 
     */
    protected $pid;
    /**
     * 用户ID 
     * @var  int 
     */
    protected $uid;
    /**
     * 分组ID 
     * @var  int 
     */
    protected $gid;
    /**
     * 上传文件 
     * @var  string 
     */
    protected $upload;



    /**
     * @return  Permision   
     */
    public function setPid(int $pid) {
        $this->pid=$pid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getPid() : int {
        return $this->pid;
    }


    /**
     * @return  Permision   
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
     * @return  Permision   
     */
    public function setGid(int $gid) {
        $this->gid=$gid;
        return $this;
    }

    /**
     * @return  int   
     */
    public function getGid() : int {
        return $this->gid;
    }


    /**
     * @return  Permision   
     */
    public function setUpload(string $upload) {
        $this->upload=$upload;
        return $this;
    }

    /**
     * @return  string   
     */
    public function getUpload() : string {
        return $this->upload;
    }
}

/**
* DTA FILE:
; 权限表
pid bigint(20) auto primary comment="权限ID"
uid bigint(20) unique  comment="用户ID"
gid bigint(20) unique  comment="分组ID"
upload enum('Y','N') default='N' comment="上传文件"
*/