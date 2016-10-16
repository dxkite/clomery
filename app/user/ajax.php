<?php
namespace user;

use Page;
use Request;
use Query;
use UserManager;
use Session;

class ajax
{

    const REG_EMAIL='/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    // 密码随意
    // const REG_PASSWD='/^([0-9a-zA-Z\_`!~@#$%^*+=,.?;\'":)(}{/\\\|<>&\[\-]|\])+$/';
    const REG_UNAME='/^[\w\x{4e00}-\x{9aff}]{4,12}$/u';


    public function main()
    {
        Page::getController()->json();
        $json=Request::json();
        if (isset($json['type'])) {
            switch ($json['type']) {
                case 'verify':
                if (isset($json['code'])) {
                    $r=(strtoupper(Session::get('human_varify'))===strtoupper($json['code']));
                    if ($r===true) {
                        Session::set('verify_code', $json['code']);
                    }
                    return ['return'=>$r];
                }
                case 'checkuser':
                if (isset($json['user'])) {
                    return ['return'=>UserManager::userExist($json['user'])];
                }break;
                 case 'checkemail':
                if (isset($json['email'])) {
                    return ['return'=>UserManager::emailExist($json['email'])];
                }break;
                case 'signup':
                if (isset($json['user']) && isset($json['passwd']) && isset($json['email']) && isset($json['code'])) {
                    return self::signup($json['user'], $json['passwd'], $json['email'],$json['code']);
                }break;
                case 'signin':
                if (isset($json['user']) && isset($json['passwd']) && isset($json['keep'])) {
                    return self::signin($json['user'], $json['passwd'],$json['keep']);
                }break;
            }
        }
        return ['return'=>-1,'message'=>'unsupport json'];
    }

    public function signup(string $user, string $passwd, string  $email, string $code)
    {   
        // var_dump(Session::get('verify_code'),$code);
        if (strtoupper(Session::get('verify_code'))!==strtoupper($code)) {
            $message='invaild verify code';
        } elseif (!preg_match(self::REG_UNAME, $user)) {
            $message='invaild username';
        } elseif (!preg_match(self::REG_EMAIL, $email)) {
            $message='invaild email';
        } else {
            if ($id=UserManager::signup($user, $passwd, $email)) {
                return ['return'=>true,'message'=>'signup success','uid'=>$id];
            }
            $message='signup error('.$id.')';
        }
        return ['return'=>false,'message'=>$message];
    }

    public function signin(string $name, string $passwd,string $keep)
    {
        if (!preg_match(self::REG_UNAME, $name)) {
            $message='invaild username';
        } else {
            if (($rt=UserManager::signin($name, $passwd,$keep))===0) {
                return ['return'=>true];
            }
            return ['return'=>false,'erron'=>$rt];
        }
        return ['return'=>false,'message'=>$message];
    }
}
