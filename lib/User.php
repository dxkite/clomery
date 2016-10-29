<?php
/*
 用户接口总集
*/
class User
{

    private $base=null;
    private $permission=null;

    public function hasSignin()
    {
        if ($info=Common_User::hasSignin()) {
            $this->base=$info;
            return true;
        } else {
            return false;
        }
    }
    
    public function __get(string $name)
    {
        if (isset($this->base[$name])){
            return $this->base[$name];
        }
        return null;
    }
    
    public function permission()
    {
        if (is_null($this->permission)) {
            $this->permission=new User_Permission($this->base['uid']);
        }
        return $this->permission;
    }
}
