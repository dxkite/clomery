<?php
namespace model;

class Token
{
    protected function getAliveTime()
    {
        return 3600; //1小时
    }
    // 生成令牌
    protected function generateToken(int $uid, string $tokenname)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.SITE_VERSION.'-'.$id.'-'.microtime(true).'-'.$mis);
    }

    // 创建令牌
    public function createToken(int $uid, string $name, string $value='')
    {
        // 存在同名Token则更新
        if ($fetch=Query::where('user_token', ['uid', 'token'], ['uid'=>$uid, 'name'=>$name])->fetch()) {
            return self::refreshToken($fetch['uid'], $fetch['token']);
        } else { // 创建新Token
            $verify=self::generateToken($uid, $name);
            $time=time();
            $token=Query::insert('user_token', ['uid'=>$uid, 'token'=>$verify, 'time'=>$time, 'ip'=>Request::ip(), 'name'=>$name, 'expire'=>$time+self::getAliveTime(), 'value'=>$value]);
            return ['tid'=>$token,'token'=>$verify,'time'=>$time];
        }
    }

    // 刷新过期时间
    public function refreshToken(int $uid, string $token)
    {
        $time=time()+self::getAliveTime();
        $new =self::generateToken($tid, $token);
        if (Query::update('user_token', 'expire ='.$time.', token=:new_token', 'uid=:uid AND token = :token ', ['uid'=>$uid, 'token'=>$token, 'new_token'=>$new])) {
            return  ['tid'=>$tid, 'token'=>$new, 'time'=>$time];
        }
        return false;
    }

    // 验证令牌值
    public function verifyTokenValue(int $uid, string $token, string $value)
    {
        return Query::where('user_token', 'uid', '`uid` =:uid AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) AND `value` =:value', ['uid'=>$uid, 'token'=>$token, 'value'=>$value])->fetch()?true:false;
    }

    // 验证令牌是否过期
    public function verifyToken(int $uid, string $token)
    {
        return Query::where('user_token', 'uid', 'uid =:uid AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) ', ['uid'=>$uid, 'token'=>$token ])->fetch()?true:false;
    }
    
    // 删除令牌
    public function deleteToken(int $uid,string $token)
    {
        return Query::update('user_token','`expire`=UNIX_TIMESTAMP()-3600',['uid'=>$uid,'token'=>$token]);
    }
}
