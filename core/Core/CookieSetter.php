<?php
namespace Core;

class CookieSetter
{
    public $name;
    public $value;
    public $httponly=false;
    public $path='/';
    public $domain=null;
    public $expire=1440;
    public $secure=false;
    public function __construct(string $name, string $value, int $expire=1440)
    {
        $this->name=$name;
        $this->value=$value;
        $this->expire=$expire;
    }
    public function httpOnly(bool $set=true)
    {
        $this->httponly=$set;
        return $this;
    }
    public function secure(bool $set=true)
    {
        $this->secure=$set;
        return $this;
    }
    public function path(string $set='/')
    {
        $this->path=$set;
        return $this;
    }
    public function expire(int $set=1440)
    {
        $this->expire=$set;
        return $this;
    }
    public function domain(string $set)
    {
        $this->domain=$set;
        return $this;
    }
    public function get()
    {
        return $this->value;
    }
    public function set()
    {
        return setcookie($this->name, $this->value, time()+$this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }
}
