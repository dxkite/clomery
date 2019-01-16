<?php
namespace dxkite\support\visitor;

use suda\core\Request;
use suda\core\Session;
use suda\core\Cookie;
use suda\core\Storage;
use dxkite\support\visitor\Visitor;

class Context
{
    private $request;
    private $sessionId;
    private $cookieName='__visitor';
    private $visitor=null;

    protected static $instance=null;

    protected function __construct()
    {
        $this->initSession();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance=new  Context;
        }
        return self::$instance;
    }

    public function getSessionId()
    {
        return $this->sessionId;
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
        return $this->visitor;
    }

    public function loadVisitor()
    {
        if (self::hasSession('__visitor')) {
            $visitor = unserialize(self::getSession('__visitor'));
            if ($visitor && $visitor->isLive()) {
                debug()->info(__('load_user_session $0:$1 token $2', $visitor->getId(), $visitor->getToken(), $visitor->getMaskToken()));
                debug()->addDump('visitor', $visitor);
                $visitor->simulateIfy();
                $visitor->refershPermission();
                $this->visitor = $visitor;
                return;
            }
        }
        $this->visitor=new Visitor;
    }

    public static function encodeCookieName(string $name, string $mask)
    {
        $mask=md5($mask, true);
        $name=pack('A*', $name);
        return bin2hex($mask^$name);
    }

    public static function decodeCookieName(string $data, string $mask)
    {
        $mask=md5($mask, true);
        if ($hash=hex2bin($data)) {
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
    
    public function delSession(string $name)
    {
        unset($_SESSION[$name]);
    }

    public function hasSession(string $name)
    {
        return array_key_exists($name, $_SESSION);
    }

    public function destroySession()
    {
        session_unset();
        session_destroy();
    }
    
    public function updateSession()
    {
        $this->destroySession();
        $this->initSession(false);
        debug()->trace('update session '.$this->sessionId);
    }

    public function updateSessionId()
    {
        $data = $_SESSION;
        $this->destroySession();
        $this->initSession(false);
        foreach ($data as $name => $value) {
            if (!is_null($value)) {
                $_SESSION[$name] = $value;
            }
        }
        debug()->trace(__('update session id $0', $this->sessionId));
    }

    public function saveVisitor(Visitor $visitor)
    {
        $this->updateSessionId();
        self::setSession('__visitor', serialize($visitor));
    }
    
    public function removeVisitor()
    {
        $this->updateSession();
        return self::delSession('__visitor');
    }

    public function getCookieName()
    {
        return $this->encodeCookieName($this->cookieName, $this->sessionId);
    }

    private function initSession(bool $loadFromCookie=true)
    {
        $path=DATA_DIR.'/session';
        $sessionCookie = conf('session.name', '__session');
        $id = null;
        if ($loadFromCookie && cookie()->has($sessionCookie)) {
            $id = cookie()->get($sessionCookie);
        }
        if (!$loadFromCookie || strlen($id) < 32) {
            $id=md5(conf('session.secret', ROOT_PATH.SUDA_VERSION).request()->ip().uniqid());
            hook()->exec('support:sessionId::init', [&$id]);
        }
        session_id($id);
        if (storage()->mkdirs($path)) {
            session_save_path($path);
        }
        session_name(conf('session.name', '__session'));
        session_cache_limiter(conf('session.limiter', 'private'));
        session_cache_expire(conf('session.expire', 0));
        session_set_cookie_params(conf('session.lifetime', 3600), '/', null, false, true);
        session_start();
        $this->sessionId=session_id();
        debug()->trace('start session '.$this->sessionId);
    }
}
