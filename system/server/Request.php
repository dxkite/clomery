<?php
namespace server;

use server\core\Value;

final class Request extends Value
{
    private $get=null;
    private $post=null;
    private $url;
    public function json()
    {
        $str=file_get_contents('php://input');
        return core\Json::decode($str, true);
    }
    public function get(string $name='')
    {
        if (is_null(self::$get)) {
            self::$get=new Value($_GET);
        }
        if ($name) {
            return self::$get->$name;
        } else {
            return self::$get;
        }
    }

    public function post(string $name='')
    {
        if (is_null(self::$post)) {
            self::$post=new Value($_POST);
        }
        if ($name) {
            return self::$post->$name;
        } else {
            return self::$post;
        }
    }

    public static function ip()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
        }
        return $ip;
    }

    public function ipAddress($ip)
    {
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(@file_get_contents($url), true);
        return $ip;
    }

    public function hasPost()
    {
        return count($_POST);
    }
    public function hasGet()
    {
        return count($_GET);
    }
}
