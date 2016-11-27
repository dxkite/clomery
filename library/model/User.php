<?php
namespace model;

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

    public function signUp(string $name, string $email, string $password, string $usage='sign')
    {
        try {
            Query::begin();
            $uid=Query::insert('user', ['name'=>$name, 'password'=>password_hash($password, PASSWORD_DEFAULT), 'email'=>$email]);
            $token=self::createToken($uid, $usage);
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            $message=$e->getMessage();
            if ($e->getCode()===23000) {
                preg_match('/key \'(\w+)\'/', $e->getMessage(), $match);
                $message='Duplicate user '.$match[1];
            }
            return new APIError('signUpError', $message);
        }
        return new APIResult(true, $token);
    }

    protected function createToken(string $uid, string $usage)
    {
        $verify=self::tokenString($uid);
        $time=time();
        $token=Query::insert('user_token', ['uid'=>$uid, 'token'=>$verify, 'time'=>$time, 'ip'=>Request::ip(), 'name'=>$usage, 'expire'=>$time+3600]);
        return ['tid'=>$token,'token'=>$verify,'time'=>$time];
    }
    
    protected function tokenString($id)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.SITE_VERSION.'-'.$id.'-'.microtime(true).'-'.$mis);
    }

    public function refreshToken(int $tid, string $token)
    {
        $time=time()+3600;
        $new =self::tokenString($tid);
        if (Query::update('user_token',
        'expire ='.$time.', token=:new_token',
        'tid=:tid AND token = :token ',
        ['tid'=>$tid, 'token'=>$token, 'new_token'=>$new])) {
            return new APIResult(true, ['tid'=>$tid, 'token'=>$new, 'time'=>$time]);
        }
        return new APIError('tokenRefreshError', ['tid'=>$tid, 'token'=>$token]);
    }

    public function verifyTokenByToken(string $token)
    {
        return Query::where('user_token', 'uid', 'LOWER(token) = LOWER(:token) AND `name` =:name', ['token'=>$token])->fetch()?true:false;
    }

    public function verifyTokenByValue(int $tid, string $token, string $value)
    {
        return Query::where('user_token', 'uid',
        '`tid` =:tid AND `token`=:token and `value`=:value',
        ['token'=>$token, 'value'=>$value, 'tid'=>$tid]
        )->fetch()?true:false;
    }
}
