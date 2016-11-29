<?php
use helper\Value;

final class Request extends Value
{
    private $get=null;
    private $post=null;
    private $url;

    public function __construct()
    {
        self::init();
    }

    public function init()
    {
        // 预处理
        if (preg_match('/^\/\?\//', $_SERVER['REQUEST_URI'])) {
            $preg='/^(\/\?(\/[^?]*))(?:[?](.+))?$/';
            preg_match($preg, $_SERVER['REQUEST_URI'], $match);
            $_SERVER['PHP_SELF']=$match[1];
            if (isset($match[3])) {
                parse_str($match[3], $_GET);
            }
            $this->url=$match[2];
        } else {
            $preg='/(.*)\/index.php(\/[^?]*)?$/';
            preg_match($preg, $_SERVER['PHP_SELF'], $match);
            $this->url=isset($match[2])?$match[2]:'/';
        }
        if (!isset($_SERVER['PATH_INFO'])) {
            $_SERVER['PATH_INFO']=$this->url;
        }
       
        if (is_null($this->post)) {
            $this->post=new Value($_POST);
        }
        if (is_null($this->get)) {
            $this->get=new Value($_GET);
        }
    }

    public function json()
    {
        $str=file_get_contents('php://input');
        return helper\Json::decode($str, true);
    }
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    public function url()
    {
        return $this->url;
    }
    
    public function set(string $name, $value)
    {
        $this->get->$name=$value;
        return $this;
    }

    public function get(string $name='')
    {
        if ($name) {
            return $this->get->$name;
        } else {
            return $this->get;
        }
    }

    public function post(string $name='')
    {
        if ($name) {
            return $this->post->$name;
        } else {
            return $this->post;
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

    public function ip2Address($ip)
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
    public function isJson(){
        return isset($_SERVER['CONTENT_TYPE']) && preg_match('/json/i',$_SERVER['CONTENT_TYPE']);
    }
}
