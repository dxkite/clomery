<?php
namespace cn\atd3\visitor;

use suda\core\Request;
use suda\core\Session;
use suda\core\Cookie;
use suda\core\Storage;
use cn\atd3\visitor\Visitor;

class Context
{
    private $request;
    private $sessionId;
    private $cookieName='__visitor';
    private $requestSession=true;
    private $visitor=null;

    protected static $instance;

    protected function __construct()
    {
        $this->initSession();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance=new  self;
        }
        return self::$instance;
    }

    private function getSessionId()
    {
        return $this->session_id;
    }

    public function setRequest(Request $request)
    {
        $this->request=$request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setVisitor(Visitor $visitor)
    {
        $this->visitor=$visitor;
        return $this;
    }

    public function getVisitor():Visitor
    {
        // TODO: fix it
        // if (is_null($this->visitor)) {
        //     $this->visitor=$this->loadFromCookie();
        // }
        return $this->visitor;
    }


    public static function encodeCookieName(string $name, string $mask)
    {
        $mask=md5($mask, true);
        $name=pack('A*', $name);
        return base64_encode($mask^$name);
    }

    public static function decodeCookieName(string $data, string $mask)
    {
        $mask=md5($mask, true);
        if ($hash=base64_decode($data)) {
            return unpack('A*name', $hash^$mask)['name'];
        }
        return false;
    }

    public function setSession(string $name, $value)
    {
        $_SESSION[$name]=$value;
        return isset($_SESSION[$name]);
    }

    public function getSession(string $name='', $default=null)
    {
        if ($name) {
            return isset($_SESSION[$name])?$_SESSION[$name]:$default;
        } else {
            return $_SESSION;
        }
    }

    public function hasSession(string $name)
    {
        return session_is_registered($name);
    }

    public function destroySession()
    {
        session_unset();
    }

    public function cookieVisitor(Visitor $visitor)
    {
        return Cookie::set($this->getCookieName(), $visitor->getMaskToken());
    }
    
    public function getCookieName()
    {
        return $this->encodeCookieName($this->cookieName, $this->sessionId);
    }

    private function initSession()
    {
        $path=DATA_DIR.'/session';
        if ($this->requestSession) {
            session_id(md5(Request::getInstance()->signature()));
        }
        Storage::mkdirs($path);
        session_save_path($path);
        session_name(conf('session.name', '__session'));
        session_cache_limiter(conf('session.limiter', 'private'));
        session_cache_expire(conf('session.expire', 0));
        session_start();
        $this->sessionId=session_id();
        debug()->trace('start session '.$this->sessionId);
    }
}
