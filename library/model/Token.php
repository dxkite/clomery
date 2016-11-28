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
    public function getRefreshAliveTime()
    {
        return 6404800; // 一个星期
    }
    // 生成令牌
    protected function generate(int $user, string $tokenname)
    {
        static $mis='5246-687261-5852-6C';
        return md5('DXCore-'.SITE_VERSION.'-'.$user.'-'.microtime(true).'-'.$mis.'-'.$tokenname);
    }

    // 创建令牌
    public function create(int $user, int $client, string $client_token, string $value=null)
    {
        // 客户端可用
        if (Client::check($client, $client_token)) {
            // 存在同名Token则更新
            if ($fetch=Query::where('token', ['id', 'value'], '`user`=:user AND `client`=:client AND `expire` > UNIX_TIMESTAMP()', ['user'=>$user, 'client'=>$client])->fetch()) {
                return self::refresh($fetch['id'], $fetch['value']);
            } else { // 创建新Token
                $verify=self::generate($user, $client);
                if (!$value) {
                    $value=self::generate($user, $verify);
                }
                $time=time();
                $token=Query::insert('token', ['user'=>$user, 'token'=>$verify, 'time'=>$time, 'ip'=>Request::ip(), 'client'=>$client, 'expire'=>$time+self::getAliveTime(), 'value'=>$value]);
                return ['id'=>$token,'token'=>$verify,'time'=>$time,'value'=>$value];
            }
        }
        return false;
    }

    // 刷新过期时间
    public function refresh(int $id, string $value)
    {
        $time=time()+self::getAliveTime();
        $new =self::generate($id, $value);
        $refresh=self::generate($id, $new);
        if (Query::update('token', 'expire ='.$time.', token=:new_token,value=:refresh', 'id=:id AND UNIX_TIMESTAMP() < `time` + :refresh_alive AND value = :value ', ['id'=>$id, 'value'=>$value, 'new_token'=>$new, 'refresh'=>$refresh, 'refresh_alive'=>self::getRefreshAliveTime()])) {
            return  ['id'=>$id, 'token'=>$new, 'time'=>$time,'value'=>$refresh];
        }
        return false;
    }

    // 验证令牌值
    public function verifyValue(int $id, string $token, string $value)
    {
        return Query::where('token', 'user', '`id` =:id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) AND `value` =:value', ['id'=>$id, 'token'=>$token, 'value'=>$value])->fetch()?true:false;
    }

    // 验证令牌是否过期
    public function verify(int $id, string $token)
    {
        return Query::where('token', 'user', 'id =:id AND `expire` > UNIX_TIMESTAMP() AND LOWER(token) = LOWER(:token) ', ['id'=>$id, 'token'=>$token ])->fetch()?true:false;
    }
    
    // 删除令牌
    public function delete(int $id, string $token)
    {
        return Query::update('token', '`expire`=UNIX_TIMESTAMP()', ['id'=>$id, 'token'=>$token]);
    }
}
