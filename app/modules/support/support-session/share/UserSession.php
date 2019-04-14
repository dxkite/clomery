<?php
namespace support\session;

use suda\framework\Request;
use suda\framework\Response;
use suda\framework\http\Cookie;
use suda\application\Application;
use support\session\table\SessionTable;
use support\openmethod\MethodParameterInterface;
use support\openmethod\processor\ResultProcessor;

class UserSession implements MethodParameterInterface, ResultProcessor
{
    /**
     * 会话ID
     *
     * @var string
     */
    protected $id;

    /**
     * 会话组
     *
     * @var string
     */
    protected $group;

    /**
     * 会话Token
     *
     * @var string
     */
    protected $token;

    /**
     * 用户ID
     *
     * @var string
     */
    protected $userId;

    /**
     * 过期时间
     *
     * @var int
     */
    protected $expireTime;

    /**
     * 心跳时间
     *
     * @var integer
     */
    protected static $beat = 60;

    /**
     * 保存会话
     *
     * @param string $userId 用户ID
     * @param string $ip  用户IP
     * @param integer $expireIn 过期时间
     * @param string $group 会话组
     * @return UserSession
     */
    public static function save(string $userId, string $ip, int $expireIn = 0, string $group = 'system'): UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        $token = \md5(\microtime(true).$userId.$group.$expireIn, true);
        $session->token = static::encode($userId.':'.$token);
        // 用户会话有效
        if ($data = $table->read('id', 'expire', 'token', 'grantee')->where([
            'ip' => $ip,
            'group' => $group,
            'grantee' => $userId,
            'expire' => ['>', time()],
        ])->one()) {
            $session->id = $data['id'];
            $session->userId = $data['grantee'];
            // 小于10倍心跳时长则更新
            $limit = time() + static::$beat * 10;
            $write = $table->write('token', static::encode($token));
            if ($data['expire'] < $limit && $expireIn === 0) {
                $session->expireTime = $session->expireTime + $beat;
                $write->write('expire', $session->expireTime);
            } else {
                $session->expireTime = time() + $expireIn;
                $write->write('expire', $session->expireTime);
            }
            $write->where(['id' => $data['id']])->ok();
        } else {
            $session->expireTime = time() + $expireIn;
            $session->userId = $userId;
            $session->id = $table->write([
                'group' => $group,
                'grantee' => $userId,
                'expire' => $session->expireTime,
                'token' => static::encode($token),
                'time' => time(),
                'ip' => $ip,
            ])->id();
        }
        return $session;
    }


    /**
     * 设置会话过期
     *
     * @param string $user
     * @param string $group
     * @return boolean
     */
    public static function expire(string $user, string $group = 'system'): bool
    {
        $table = new SessionTable;
        return $table->write([
            'expire' => time()
        ])->where(['group' => $group,])->ok();
    }

    /**
     * 从Token中登陆
     *
     * @param string $token
     * @param string $ip
     * @param string $group
     * @return UserSession
     */
    public static function load(string $token, string $ip, string $group = 'system'):UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        // 会话无效
        $session->expireTime = time() ;
        $session->userId = '';
        $session->token = '';
        $target = static::decode($token);

        if (strlen($token) < 10 || strlen($token) > 32 || strpos($target, ':') === false) {
            return $session;
        }
        
        list($user, $token) = \explode(':', $target, 2); 

        if ($data = $table->read('id', 'expire', 'token', 'grantee')->where([
            'grantee' => $user,
            'ip' => $ip,
            'group' => $group,
            'token' => static::encode($token),
            'expire' => ['>', time()],
        ])->one()) {
            $session->id = $data['id'];
            $session->token = static::encode($user.':'. static::decode($data['token']));
            $session->expireTime = $data['expire'];
            $session->userId = $data['grantee'];
            // 小于10倍心跳时长则更新
            $limit = time() + static::$beat * 10;
            if ($data['expire'] < $limit) {
                $session->expireTime = $session->expireTime + static::$beat;
                $table->write('expire', $session->expireTime)->where(['id' => $data['id']])->rows();
            }
        }
        return $session;
    }

    /**
     * 模拟用户
     *
     * @param string $userId
     * @param integer $exporeIn
     * @param string $group
     * @return UserSession
     */
    public static function simulate(string $userId, int $exporeIn, string $group = 'system'):UserSession
    {
        $session = new static;
        $session->group = $group;
        $session->expireTime = time() + $exporeIn;
        $session->userId = $userId;
        $session->token = static::encode(\md5(\microtime(true).$userId.$group.$expireIn, true));
        return $session;
    }

    /**
     * 处理返回结果
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return mixed
     */
    public function processor(Application $application, Request $request, Response $response)
    {
        $response->setCookie('x-'.$this->group.'-token', $this->token);
        $response->setCookie('x-token-group', $this->group);
        return [
            'id' => $this->id,
            'user' => $this->userId,
            'token' => $this->token,
            'expire_time' => $this->expireTime,
            'group' => $this->group,
        ];
    }

    /**
     * 换表编码
     *
     * @param string $data
     * @return string
     */
    public static function encode(string $data):string
    {
        return str_replace(['=','/','+'], ['','-','_'], base64_encode($data));
    }

    /**
     * 换表解码
     *
     * @param string $data
     * @return string
     */
    public static function decode(string $data):string
    {
        return \base64_decode(str_replace(['-','_'], ['/','+'], $data));
    }

    /**
     * 从请求中创建
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @return self
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, Application $application, Request $request)
    {
        $group = $request->getHeader('x-token-group', $request->getCookie('x-token-group', 'system'));
        return static::createFromRequest($request, $group);
    }

    /**
     * 从响应中创建对象
     *
     * @param \suda\framework\Request $request
     * @param string $group
     * @return self
     */
    public static function createFromRequest(Request $request, string $group)
    {
        $token = $request->getHeader('x-'.$group.'-token', $request->getCookie('x-'.$group.'-token', ''));
        $session = UserSession::load($token, $request->getRemoteAddr(), $group);
        if ($session->isGuest() && strlen($token) > 32) {
            if (\strpos($token = 'debug:') === 0 && substr_count($token, ':', 32) === 2) {
                list($debug, $user, $password) = \explode(':', $token, 3);
                if ($password === $application->conf('app.system-debug-token')) {
                    $session = UserSession::simulate($user, 3600, $group);
                }
            }
        }
        return $session;
    }

    /**
     * 判断是否登陆
     *
     * @return boolean
     */
    public function isGuest():bool
    {
        return $this->expireTime < time();
    }

    /**
     * Get 会话ID
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 会话ID
     *
     * @param  string  $id  会话ID
     *
     * @return  self
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get 会话组
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set 会话组
     *
     * @param  string  $group  会话组
     *
     * @return  self
     */
    public function setGroup(string $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get 会话Token
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set 会话Token
     *
     * @param  string  $token  会话Token
     *
     * @return  self
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get 用户ID
     *
     * @return  string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set 用户ID
     *
     * @param  string  $userId  用户ID
     *
     * @return  self
     */
    public function setUserId(string $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get 过期时间
     *
     * @return  int
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * Set 过期时间
     *
     * @param  int  $expireTime  过期时间
     *
     * @return  self
     */
    public function setExpireTime(int $expireTime)
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * Get 心跳时间
     *
     * @return  integer
     */
    public static function getBeat()
    {
        return static::$beat;
    }

    /**
     * Set 心跳时间
     *
     * @param  integer  $beat  心跳时间
     *
     * @return  self
     */
    public static function setBeat($beat)
    {
        static::$beat = $beat;
    }
}
