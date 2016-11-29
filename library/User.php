<?php

class User
{
    public function signUp(string $name, string $email, string $passwd)
    {
        // 获取网站操作权限
        $client=Kite::getClient();
        // 生成6位邮箱验证码 
        $code=substr(base64_encode(md5('5246-687261-5852-6C'+time())), 0, 6);
        if ($get=model\User::signUp($name, $email, $passwd, $client['id'], $client['token'], $code)) {
            Cookie::set('user_token', base64_encode($get['id'].'.'.$get['token']), 3600)->httpOnly();
            return $get['user_id'];
        }
        return false;
    }

    public function signIn(string $name, string $passwd)
    {
        // 获取网站操作权限
        $client=Kite::getClient();
        if ($get=model\User::signIn($name, $passwd, $client['id'], $client['token'])) {
            Cookie::set('user_token', base64_encode($get['id'].'.'.$get['token'].'.'.$get['value']), 3600)->httpOnly();
            return $get['user_id'];
        }
        return false;
    }
    public function signOut() 
    {

    }
    public function getSignInUserId()
    {
        if (Cookie::has('user_token')) {
            $token=base64_decode(Cookie::get('user_token'));
            if (preg_match('/^(\d+)[.]([a-zA-Z0-9]{32})(?:[.]([a-zA-Z0-9]{32}))?$/', $token, $match)) {
                if ($uid=model\Token::verify(intval($match[1]), $match[2])) {
                    return intval($uid);
                } elseif (isset($match[3])) {
                    // 获取网站操作权限
                    $client=Kite::getClient();
                    // 一次心跳
                    if ($get=model\Token::refresh(intval($match[1]), intval($client['id']), $client['token'], $match[3]))
                    {
                         Cookie::set('user_token', base64_encode($get['id'].'.'.$get['token'].'.'.$get['value']), 3600)->httpOnly();
                         return intval(model\Token::verify($get['id'],$get['token']));
                    }
                }
            }
        }
        return 0;
    }
}
