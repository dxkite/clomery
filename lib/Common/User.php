<?php
// TODO: 是否限制IP注册
class Common_User
{
    public static function userExist(string $user):bool
    {
        return self::user2Id($user)!==0;
    }
    public static function user2Id(string $user):int
    {
        $q=new Query('SELECT uid FROM #{users} where LOWER(uname) = LOWER(:uname) LIMIT 1;');
        $q->values(['uname'=>$user]);
        if ($get=$q->fetch()) {
            return $get['uid'];
        }
        return 0;
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
    public static function createVerify(int $uid):string
    {
        // 猜猜是啥
        static $mis='5246-687261-5852-6C';
        $q='UPDATE `atd_users` SET `verify` = :verify , `expriation`=:time  WHERE `atd_users`.`uid` = :uid;';
        $verify=md5('DXCore-'.CORE_VERSION.'-'.$uid.'-'.time().'-'.$mis);
        if ((new Query($q, ['verify'=>$verify, 'time'=>time(), 'uid'=>$uid]))->exec()) {
            return $verify;
        }
        return '';
    }
    public static function verify(int $uid, string $hash, int $expriaton=0)
    {
        return (new Query('UPDATE `#{users}` SET `email_verify` =\'Y\' WHERE `uid`=:uid AND `verify` = :hash AND `expriation` > :expriation LIMIT 1;', ['uid'=>$uid, 'hash'=>$hash, 'expriation'=>$expriaton]))->exec();
    }
    public static function signUp(string $user, string $passwd, string $email):int
    {
        $token=md5(Request::ip().time());
        if (($q=new Query('INSERT INTO #{users} (`uname`,`upass`,`email`,`signup`,`lastip`,`token`) VALUES ( :uname, :passwd, :email, :signup ,:lastip , :token );'))->values([
            'uname'=>$user,
            'passwd'=>password_hash($passwd, PASSWORD_DEFAULT),
            'email'=>$email,
            'signup'=>time(),
            'lastip'=>Request::ip(),
            'token'=>$token,
        ])->exec()) {
            $uid=$q->lastInsertId();
            // 登陆日志记录
            (new Query('INSERT INTO `#{signin_historys}` (`uid`,`ip`,`time`) VALUES (:uid,:ip,:time)'))->values([
                 'uid'=>$uid,
                'ip'=>Request::ip(),
                'time'=>time(),
            ])->exec();
            Session::regenerate(true);
            // 设置登陆状态
            Session::set('signin', true);
            // 登陆信息
            Session::set('user_id', $uid);
            // 登陆状态保留（只能临时用~~)
            Session::set('token', $token.$uid);
            //信息缓存
            Cache::set('user:'.$uid, $user);
            Cache::set('uid:'.$user, $uid);
            return $uid;
        }
        return 0;
    }
    public static function signIn(string $name, string $passwd, string $keep):int
    {
        $token=md5(Request::ip().time());
        if ($get=(new Query('SELECT `upass`,`uid` FROM #{users} WHERE LOWER(uname)=LOWER(:uname)LIMIT 1;'))->values(['uname'=>$name])->fetch()) {
            //信息缓存
            Cache::set('user:'.$get['uid'], $name);
            Cache::set('uid:'.$name, $get['uid']);
            if (password_verify($passwd, $get['upass'])) {
                if ((new Query('UPDATE `#{users}` set signin=:signin,lastip:=:lastip,token=:token where uid=:uid LIMIT 1;'))->values([
                    'uid'=>$get['uid'],
                    'signin'=>time(),
                    'lastip'=>Request::ip(),
                    'token'=>$token,
                ])->exec()) {
                    // 登陆日志记录
                    (new Query('INSERT INTO `#{signin_historys}` (`uid`,`ip`,`time`) VALUES (:uid,:ip,:time)'))->values([
                        'uid'=>$get['uid'],
                        'ip'=>Request::ip(),
                        'time'=>time(),
                    ])->exec();
                    Session::regenerate(true);
                    // 设置登陆状态
                    Session::set('signin', true);
                    // 登陆信息
                    Session::set('user_id', $get['uid']);
                    Session::set('token', $token.$get['uid']);
                    if ($keep) {
                        // 登陆状态保留
                        Cookie::set('token', $token.$get['uid'], 2592000)->httpOnly();
                    }
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
    public static function getSigninLogs(int $uid) :array
    {
        if ($history = (new Query('SELECT `ip`,`time`  FROM `#{signin_historys}` WHERE `uid` = :uid  ORDER BY `time` DESC LIMIT 5; '))
        -> values(
            ['uid'=>$uid]
        )->fetchAll()) {
            return $history;
        }
        return [];
    }
    public static function hasSignin()
    {
        if ($get=self::getLastUserInfo()) {
            // 设置登陆状态
            Session::set('signin', true);
            // 登陆信息
            Session::set('user_id', $get['uid']);
            return $get;
        }
        return false;
    }

    public static function getLastUserInfo()
    {
        static $info=null;
        if ($info) {
            return $info;
        }
        $token=Cookie::has('token')?Cookie::get('token'):(Session::has('token')?Session::get('token'):'');
        preg_match('/^([a-zA-z0-9]{0,32})(\d+)$/', $token, $match);
        if (count($match)>0 && $last=(new Query('SELECT `uid`,`lastip`,`uname` as `name`,`signup`,`email`,`email_verify` FROM `#{users}` WHERE uid=:uid AND token=:token LIMIT 1;'))
            ->values([
                    'token'=>$match[1],
                    'uid'=>$match[2]
                ])->fetch()) {
            $info=$last;
            return $last;
        }
        return false;
    }
    
    public static function signOut()
    {
        $uid=Session::get('user_id');
        (new Query('UPDATE `#{users}` SET `token` = \'\' WHERE `#{users}`.`uid` = :uid ;'))->values(['uid'=>$uid])->exec();
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
    
    public static function setAvatar(int $uid, int $avatar)
    {
        $q='UPDATE `#{user_info}` SET `avatar` = :avatar WHERE `#{user_info}`.`uid` = :uid;';
        return (new Query($q, ['uid'=>$uid, 'avatar'=>$avatar]))->exec();
    }
    public static function getPublicInfo(int $uid)
    {
        static $info=null;
        if (isset($info[$uid])) {
            return $info[$uid];
        } elseif ($info[$uid]=(new Query('SELECT `#{users}`.`uid`,`uname` as `name`,`avatar`,`signup`,`discription` FROM `#{users}`  JOIN `#{user_info}` ON  `#{user_info}`.`uid` = `#{users}`.`uid` WHERE `#{users}`.`uid`=:uid  LIMIT 1;', ['uid'=>$uid]))->fetch()) {
            return $info[$uid];
        }
        return $info;
    }
    public static function getInfo(int $uid)
    {
        $q='SELECT * FROM `#{user_info}` WHERE `uid` = :uid LIMIT 1;';
        return (new Query($q, ['uid'=>$uid]))->fetch();
    }
    public static function setDefaulInfo(int $uid, int $avatar, string $discription):bool
    {
        $q='INSERT INTO `#{user_info}` (`uid`, `avatar`,`discription`) VALUES (:uid,:avatar,:discription);';
        return (new Query($q, ['uid'=>$uid, 'avatar'=>$avatar, 'discription'=>$discription]))->exec();
    }

    public static function emailVerified(int $uid)
    {
        $q='SELECT `email_verify` FROM `#{users}` WHERE `uid` =:uid LIMIT 1;';
        if ($get=(new Query($q, ['uid'=>$uid]))->fetch()) {
            return $get['email_verify'] == 'Y';
        }
        return false;
    }
}
