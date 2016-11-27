<?php
namespace api;

use Query;
use Request;

class User
{
    public function checkEmail(string $email):bool
    {
        return Query::where('user', 'uid', 'LOWER(email) = LOWER(:email)', ['email'=>$email])->fetch()?true:false;
    }

    public function checkName(string $name):bool
    {
        return Query::where('user', 'uid', 'LOWER(name) = LOWER(:name)', ['name'=>$name])->fetch()?true:false;
    }

    public function count()
    {
        return Query::count('user');
    }

    public function tokenString(int $uid)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.SITE_VERSION.'-'.$uid.'-'.microtime(true).'-'.$mis);
    }

    public function signUp(string $name, string $email, string $password)
    {
        try {
            Query::begin();
            $uid=Query::insert('user', ['name'=>$name, 'password'=>password_hash($password, PASSWORD_DEFAULT), 'email'=>$email]);
            $verify=self::tokenString($uid);
            $token=Query::insert('user_token', ['uid'=>$uid, 'token'=>md5($verify), 'time'=>time(), 'ip'=>Request::ip(), 'name'=>'signUp', 'expire'=>time()+3600]);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            if ($e->getCode()===23000) {
                preg_match('/key \'(\w+)\'/',$e->getMessage(),$match);
                return new APIError('signUpDuplicate'.ucfirst($match[1]), 'Duplicate user '.$match[1]);
            } else {
                return new APIError('signUpError', $e->getMessage());
            }
        }
        return new APIResult(true, ['tid'=>$token, 'token'=>$verify, 'uid'=>$uid]);
    }
    
    public function refreshToken(int $tid, string $token)
    {
        $time=time()+3600;
        $new =self::tokenString($token->uid);
        if (Query::update($token->getTableName(),
        'expire ='.$time.', token=:new_token',
        'tid=:tid  AND token = :token',
        ['tid'=>$tid, 'token'=>$token, 'new_token'=>$new])) {
            return new APIResult(true, ['tid'=>$tid, 'token'=>$new, 'time'=>$time]);
        }
        return new APIError('tokenRefreshError', $token);
    }
}
