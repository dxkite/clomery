<?php
require_once __DIR__.'/../system/initailze.php';
require_once __DIR__.'/function.php';

use model\Setting;

class Three
{
    public static $request;
    public static $page;
    
    public function init()
    {
        template\Manager::loadCompile();
        template\Manager::compileAll();
        self::$request=new Request();
        self::setClient(); // 设置客户端验证
        Router::dispatch(self::$request);
        register_shutdown_function(['Three','shutdown']);
    }

    public function setClient()
    {
        if (!Cookie::has('client_id')) {
            $appid=self::getSetting('client_id', 1);
            if ($get=model\Client::get($appid)) {
                $token=self::encodeClient($get['token'],$get['id']);
                Cookie::set('client_id', $token, 3600)->httpOnly();
            } else {
                die('App is not available');
            }
        }
    }

    public function getClient()
    {
        return self::decodeClient(Cookie::get('client_id'));
    }

    public function encodeClient(string $token,int $id)
    {
        return base64_encode($token.$id);
    }

    public function decodeClient(string $code)
    {
        $code=base64_decode($code);
        preg_match('/^([a-zA-Z0-9]{32})(\d+)$/', $code, $match);
        return ['token'=>$match[1],'id'=>intval($match[2])];
    }

    public function getSetting(string $name, $default=null)
    {
        if ($get=Setting::get($name)) {
            return unserialize($get['value']);
        }
        return $default;
    }

    public function setSeting(string $name, $value)
    {
        return Setting::set($name, serialize($value));
    }
    
    public function setBaseSet()
    {
        // 评论审核
        self::setSeting('comment_verify', true);
        // 对话存活时长(每一分钟刷新在线状态)
        self::setSeting('session_alive', 60); // 1 分钟
        // 超时登陆 (超时多久后不活动则重新登陆)
        self::setSeting('token_alive', 4800); // 7天
        // 设置 Client Id
        self::setSeting('client_id', 1);
    }

    public  function shutdown(){
        Cache::gc();
    }
    public function request()
    {
        return self::$request;
    }
}
