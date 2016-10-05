<?php
namespace user;

use Page;
use Request;
use Query;
use UManager;

class ajax
{
    public function main()
    {
        Page::getController()->json();
        $json=Request::json();
        if (isset($json['type'])) {
            switch ($json['type']) {
                case 'checkuser':
                if (isset($json['user'])) {
                    return ['return'=>UManager::userExist($json['user'])];
                }break;
                 case 'checkemail':
                if (isset($json['email'])) {
                    return ['return'=>UManager::emailExist($json['email'])];
                }break;
                case 'signup':
                if (isset($json['user']) && isset($json['passwd']) && isset($json['email'])) {
                    return self::signup($json['user'], $json['passwd'], $json['email']);
                }break;
                case 'signin':
                if (isset($json['user']) && isset($json['passwd'])) {
                    return self::signin($json['user'], $json['passwd']);
                }break;
            }
        }
        return ['return'=>-1,'message'=>'unsupport json'];
    }

    public function signup(string $user, string $passwd, string  $email)
    {
        if (preg_match('/^[\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':]+$/', $user)) {
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
        if (preg_match('/^[\^\~\\`\!\@\#\$\%\&\*\(\)\-\+\=\.\/\<\>\{\}\[\]\\\|\"\':]+$/',$name)) {
            $message='invaild username';
        }  else {
            if (($rt=UManager::signin($name, $passwd))===0) {
                return ['return'=>true];
            }
            return ['return'=>false,'erron'=>$rt];
        }
        return ['return'=>false,'message'=>$message];
    }
}
