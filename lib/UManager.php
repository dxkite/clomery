<?php

class UManager
{
    public static function userExist(string $user):bool
    {
        $q=new Query('SELECT uid FROM #{users} where LOWER(uname) = LOWER(:uname) LIMIT 1;');
        $q->values(['uname'=>$user]);
        if ($get=$q->fetch()) {
            return true;
        }
        return false;
    }
    public static function emailExist(string $email):bool
    {
        $q=new Query('SELECT uid FROM #{users} where LOWER(email) = LOWER(:email) LIMIT 1;');
        $q->values(['email'=>$email]);
        if ($get=$q->fetch()) {
            return true;
        }
        return false;
    }
    public static function signUp(string $user, string $passwd, string $email):int
    {
        if (($q=new Query('INSERT INTO #{users} (`uname`,`upass`,`email`,`signup`,`lastip`,`token`) VALUES ( :uname, :passwd, :email, :signup ,:lastip , :token );'))->values([
            'uname'=>$user,
            'passwd'=>password_hash($passwd, PASSWORD_DEFAULT),
            'email'=>$email,
            'signup'=>time(),
            'lastip'=>Request::ip(),
            'token'=>md5(Request::ip().time()),
        ])->exec()) {
            $uid=$q->lastInsertId();
            // 设置登陆状态
            Session::set('signin', true);
            // 登陆信息
            Session::set('user_id', $uid);
            Session::set('user_name', $user);
            //信息缓存
            Cache::set('user:'.$uid, $user);
            Cache::set('uid:'.$user, $uid);
            return $uid;
        }
        return 0;
    }
    public static function signIn(string $name, string $passwd):int
    {
        if ($get=(new Query('SELECT `upass`,`uid` FROM #{users} WHERE LOWER(uname)=LOWER(:uname)LIMIT 1;'))->values(['uname'=>$name])->fetch()) {
            //信息缓存
            Cache::set('user:'.$get['uid'], $name);
            Cache::set('uid:'.$name, $get['uid']);
            if (password_verify($passwd, $get['upass'])) {
                if ((new Query('UPDATE `#{users}` set signin=:signin,lastip:=:lastip,token=:token where uid=:uid LIMIT 1;'))->values([
                    'uid'=>$get['uid'],
                    'signin'=>time(),
                    'lastip'=>Request::ip(),
                    'token'=>md5(Request::ip().time()),
                ])->exec()) {
                    // 设置登陆状态
                    Session::set('signin', true);
                    // 登陆信息
                    Session::set('user_id', $get['uid']);
                    Session::set('user_name', $name);
                    return 0;
                }
                return 3;// system error
            } else {
                return 2; // passwd error
            }
        } else {
            return 1; // no user
        }
    }
    public static function has_signin()
    {
        return Session::get('signin', false);
    }
    public static function signout()
    {
        // 设置登陆状态
        Session::set('signin', false);
        Session::destroy();
    }
    public static function numbers():int
    {
        $q='SELECT `TABLE_ROWS` as `size` FROM `information_schema`.`TABLES` WHERE  `TABLE_SCHEMA`="'.conf('Database.dbname').'" AND `TABLE_NAME` ="#{users}" LIMIT 1;';
        if ($a=($d=new Query($q))->fetch()) {
            return $a['size'];
        }
        return 0;
    }
}
