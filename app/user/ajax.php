<?php
namespace user;

use Page;
use Request;
use Query;
use UManager;
use Session;

class ajax
{
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
                    return ['return'=>UManager::userExist($json['user'])];
                }break;
                 case 'checkemail':
                if (isset($json['email'])) {
                    return ['return'=>UManager::emailExist($json['email'])];
                }break;
                case 'signup':
                if (isset($json['user']) && isset($json['passwd']) && isset($json['email']) && isset($json['code'])) {
                    return self::signup($json['user'], $json['passwd'], $json['email'],$json['code']);
                }break;
                case 'signin':
                if (isset($json['user']) && isset($json['passwd'])) {
                    return self::signin($json['user'], $json['passwd']);
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
        } elseif (!preg_match('/^[^\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':\s\x3f]+$/', $user)) {
            $message='invaild username';
        } elseif (!preg_match('/^\S+?[@](\w+?\.)+\w+$/', $email)) {
            $message='invaild email';
        } else {
            if ($id=UManager::signup($user, $passwd, $email)) {
                return ['return'=>true,'message'=>'signup success','uid'=>$id];
            }
            $message='signup error('.$id.')';
        }
        return ['return'=>false,'message'=>$message];
    }

    public function signin(string $name, string $passwd)
    {
        if (!preg_match('/^[^\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':\s\x3f]+$/', $name)) {
            $message='invaild username';
        } else {
            if (($rt=UManager::signin($name, $passwd))===0) {
                return ['return'=>true];
            }
            return ['return'=>false,'erron'=>$rt];
        }
        return ['return'=>false,'message'=>$message];
    }
}
