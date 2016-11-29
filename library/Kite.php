<?php
require_once __DIR__.'/../system/initailze.php';

use model\Setting;

class Kite
{
    public static $request;
    public static $page;
    public function init()
    {
        template\Manager::loadCompile();
        template\Manager::compileAll();
        self::$request=new Request();
        Router::dispatch(self::$request);
    }
    public function getSetting(string $name,$default=null)
    {
        if ($get=Setting::get($name)){
            return unserialize($get['value']);
        }
        return $default;
    }

    public function setSeting(string $name,$value) {
        return Setting::set($name,serialize($value));
    }
    
    public function setBaseSet() {
        // 评论审核
        self::setSeting('comment_verify',true);
        // 对话存活时长(每一分钟刷新在线状态)
        self::setSeting('session_alive',60); // 1 分钟
        // 超时登陆 (超时多久后不活动则重新登陆)
        self::setSeting('token_alive',4800); // 7天
    }
    public function request(){
        return self::$request;
    }
}
