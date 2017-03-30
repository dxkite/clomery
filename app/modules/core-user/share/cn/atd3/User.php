<?php
namespace cn\atd3;

class User
{
    protected $id;
    protected $info;
    
    public static $instance;
    private function __construct()
    {
        $this->id=self::getUserId();
    }
    
    public function hasSignin()
    {
        return $this->id!==0;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance =new User;
        }
        return self::$instance;
    }
    public function checkNameExist(string $name):bool
    {
        return UserCenter::checkNameExist($name);
    }

    public static function checkEmailExist(string $email):bool
    {
        return UserCenter::checkEmailExist($email);
    }

    public static function getFaildTimes():bool
    {
        return Session::set('faild_times', 0);
    }

    public static function getUserId()
    {
        if (Token::has('user')) {
            $token=base64_decode(Token::get('user'));
            if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
                if ($uid= UserCenter::tokenAvailable(intval($match[1]), $match[2])) {
                    return  intval($uid['user']);
                }
            }
        }
        return 0;
    }
    
    /**
    * 在Token可用的情况下刷新Token
    */
    public static function refreshToken()
    {
        $token=base64_decode(Token::get('user'));
        if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
            if (!isset($match[3])) {
                return false;
            }
            if ($get=$this->uc->refreshToken(intval($match[1]), $this->client, $this->token, $match[3])) {
                Token::set('user', $token= base64_encode($get['id'].'.'.$get['token'].'.'.$get['value']));
                return  true;
            } else {
                return false;
            }
        }
        return false;
    }
    public static function hasPermission(int $uid, $permissions)
    {
        $permissions=is_array($permissions)?$permissions:[$permissions];
        $needs=UserCenter::getUserPermission($uid);
        if (count($needs)>0) {
            foreach ($needs as $need) {
                if (!in_array($need, $permissions)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function checkPermission($permissions)
    {
        if ($this->id && self::hasPermission($this->id, $permissions)) {
            return true;
        }
        return false;
    }
}
