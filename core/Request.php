<?php
use Core\Value;

class Request extends Core\Value
{
    private static $_get=null;

    public static function json()
    {
        $str=file_get_contents('php://input');
        return Core\JSON::decode($str, true);
    }
    
    public static function get(string $name='')
    {
        if (is_null(self::$_get)) {
            self::$_get=Value($_GET);
        }
        if (is_null($name)) {
            return self::$_get;
        } else {
            return self::$_get->$name;
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
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public function ipAddress($ip)
    {
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(@file_get_contents($url), true);
        return $ip;
    }
}
