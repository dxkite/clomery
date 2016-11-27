<?php
namespace model;
use Query;
use dto\User as OUser;
use archive\Manager as OManager;
use dto\user\Token as Token;
use Request;
class User
{
    public function checkEmail(string $email):bool
    {
        return Query::where('user','uid','LOWER(email) = LOWER(:email)',['email'=>$email])->fetch()?true:false;
    }

    public function checkName(string $name):bool
    {
        return Query::where('user','uid','LOWER(name) = LOWER(:name)',['name'=>$name])->fetch()?true:false;
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

    public function signUp(OUser $user)
    {
        try {
            Query::begin();
            $uid=(new OManager($user))->insert();
            $verify=self::tokenString($uid);
            $token=new Token(['uid'=>$uid, 'token'=>md5($verify), 'time'=>time(),'ip'=>Request::ip(),'name'=>'signUp', 'expire'=>time()+3600]);
            $token->tid=(new OManager($token))->insert();
            Query::commit();
        } catch (\Exception $e) {
            Query::rollBack();
            return new APIError('signUpError', $e->getMessage());
        }
        return $token;
    }
    
    public function refreshToken(Token $token)
    {
        $time=3600;
        $new =self::tokenString($token->uid);
        if (Query::update($token->getTableName(),
        'expire = expire + '.$time.', token=:new_token',
        'tid=:tid  AND token = :token',
        ['tid'=>$token->tid,'token'=>$token->token,'new_token'=>$new]))
        {
            $token->token=$new;
            return $token;
        }
        return new APIError('tokenRefreshError',$token);
    }
}
