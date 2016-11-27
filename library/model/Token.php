<?php
namespace model;

use Query;
use Request;

class Token
{
    protected function getAliveTime()
    {
        return 3600; //1小时
    }
    public function getRefreshAliveTime(){
        return 6404800; // 一个星期
    }
    // 生成令牌
    protected function generateToken(int $user_id, string $tokenname)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.SITE_VERSION.'-'.$user_id.'-'.microtime(true).'-'.$mis.'-'.$tokenname);
    }

    // 创建令牌
    public function createToken(int $user_id, string $name, string $value=null)
    {
        // 存在同名Token则更新
        if ($fetch=Query::where('user_token', ['user_id', 'value'], '`user_id`=:user_id AND `name`=:name AND `expire` > UNIX_TIMESTAMP()', ['user_id'=>$user_id, 'name'=>$name])->fetch()) {
            return self::refreshToken($fetch['user_id'], $fetch['value']);
        } else { // 创建新Token
            $verify=self::generateToken($user_id, $name);
            if (!$value) {
                $value=self::generateToken($user_id, $verify);
            }
            $time=time();
            $token=Query::insert('user_token', ['user_id'=>$user_id, 'token'=>$verify, 'time'=>$time, 'ip'=>Request::ip(), 'name'=>$name, 'expire'=>$time+self::getAliveTime(), 'value'=>$value]);
            return ['id'=>$token,'token'=>$verify,'time'=>$time,'value'=>$value];
        }
    }

    // 刷新过期时间
    public function refreshToken(int $user_id, string $value)
    {
        $time=time()+self::getAliveTime();
        $new =self::generateToken($user_id, $value);
        $refresh=self::generateToken($user_id, $new);
        if (Query::update('user_token', 'expire ='.$time.', token=:new_token,value=:refresh', 'user_id=:user_id AND UNIX_TIMESTAMP() < `time` + :refresh_alive AND value = :value ', ['user_id'=>$user_id, 'value'=>$value, 'new_token'=>$new, 'refresh'=>$refresh,'refresh_alive'=>self::getRefreshAliveTime()])) {
            return  ['id'=>$user_id, 'token'=>$new, 'time'=>$time,'value'=>$refresh];
        }
        return false;
    }

    // 验证令牌值
    public function verifyTokenValue(int $user_id, string $token, string $value)
    {
        return Query::where('user_token', 'user_id', '`user_id` =:user_id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) AND `value` =:value', ['user_id'=>$user_id, 'token'=>$token, 'value'=>$value])->fetch()?true:false;
    }

    // 验证令牌是否过期
    public function verifyToken(int $user_id, string $token)
    {
        return Query::where('user_token', 'user_id', 'user_id =:user_id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) ', ['user_id'=>$user_id, 'token'=>$token ])->fetch()?true:false;
    }
    
    // 删除令牌
    public function deleteToken(int $user_id, string $token)
    {
        return Query::update('user_token', '`expire`=UNIX_TIMESTAMP()', ['user_id'=>$user_id, 'token'=>$token]);
    }
}
