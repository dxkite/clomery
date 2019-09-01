<?php

namespace support\session;

use JsonSerializable;
use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use support\session\table\SessionTable;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;
use support\openmethod\processor\ResultProcessor;
use support\visitor\event\GlobalObject;

class UserSession implements MethodParameterInterface, ResultProcessor, JsonSerializable
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
     * @var string
     */
    protected $tokenFrom;

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
     * @var string
     */
    protected $refreshToken;

    /**
     * @var int
     */
    protected $refreshExpireTime;

    /**
     * 保存会话
     *
     * @param string $userId 用户ID
     * @param string $ip 用户IP
     * @param integer $expireIn 过期时间
     * @param string $group 会话组
     * @param string $tokenFrom
     * @return UserSession
     * @throws \suda\database\exception\SQLException
     */
    public static function save(string $userId, string $ip, int $expireIn = 0, string $group = 'system', string $tokenFrom = ''): UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        $session->tokenFrom = strlen($tokenFrom) > 0 ? $tokenFrom : 'x-' . $group . '-token';

        $token = md5(microtime(true) . $userId . $group . $expireIn, true);
        $session->token = static::encode($userId . ':' . $token);
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
                $session->expireTime = $session->expireTime + static::$beat;
                $write->write('expire', $session->expireTime);
            } else {
                $session->expireTime = time() + $expireIn;
                $write->write('expire', $session->expireTime);
            }
            $write->where(['id' => $data['id']])->ok();
        } else {
            $refreshToken = md5($token . $group . $expireIn, true);
            $session->refreshToken = static::encode($userId . ':' . $refreshToken);
            $session->expireTime = time() + $expireIn;
            $session->refreshExpireTime = time() + $expireIn * 10;
            $session->userId = $userId;
            $session->id = $table->write([
                'group' => $group,
                'grantee' => $userId,
                'expire' => $session->expireTime,
                'token' => static::encode($token),
                'refresh_token' => static::encode($refreshToken),
                'refresh_expire' => $session->refreshExpireTime,
                'time' => time(),
                'ip' => $ip,
            ])->id();
        }
        return $session;
    }

    /**
     * @param string $token
     * @param string $ip
     * @param int $expireIn
     * @param string $group
     * @param string $tokenFrom
     * @return UserSession
     * @throws \suda\database\exception\SQLException
     */
    public static function refresh(string $token, string $ip, int $expireIn, string $group = 'system', string $tokenFrom = '')
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        $session->expireTime = time();
        $session->userId = '';
        $session->token = '';
        $session->tokenFrom = strlen($tokenFrom) > 0 ? $tokenFrom : 'x-' . $group . '-token';

        $target = static::decode($token);
        if (strlen($token) < 10 || strlen($token) > 32 || strpos($target, ':') === false) {
            return $session;
        }
        list($user, $token) = \explode(':', $target, 2);
        if ($data = $table->read('id', 'expire', 'refresh_token', 'grantee')->where([
            'grantee' => $user,
            'group' => $group,
            'refresh_token' => static::encode($token),
            'refresh_expire' => ['>', time()],
        ])->one()) {
            $session->id = $data['id'];
            $tokenSave = md5(static::decode(microtime(true) . $data['refresh_token']), true);
            $refreshTokenSave = md5(microtime(true) . $session->token . $token, true);
            $session->token = static::encode($user . ':' . $tokenSave);
            $session->refreshToken = static::encode($user . ':' . $refreshTokenSave);
            $session->expireTime = time() + $expireIn;
            $session->refreshExpireTime = time() + $expireIn * 10;
            $session->userId = $user;
            $table->write([
                'expire' => $session->expireTime,
                'token' => static::encode($tokenSave),
                'refresh_token' => static::encode($refreshTokenSave),
                'refresh_expire' => $session->refreshExpireTime,
                'ip' => $ip,
            ])->where(['id' => $data['id']])->ok();
        }
        return $session;
    }

    /**
     * 设置会话过期
     *
     * @param string $user
     * @param string $group
     * @return boolean
     * @throws \ReflectionException
     * @throws \suda\database\exception\SQLException
     */
    public static function expire(string $user, string $group = 'system'): bool
    {
        $table = new SessionTable;
        return $table->write([
            'expire' => time()
        ])->where(['group' => $group, 'grantee' => $user])->ok();
    }

    /**
     * 从Token中登陆
     *
     * @param string $token
     * @param string $ip
     * @param string $group
     * @param string $tokenFrom
     * @return UserSession
     * @throws \suda\database\exception\SQLException
     */
    public static function load(string $token, string $ip, string $group = 'system', string $tokenFrom = ''): UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        $session->expireTime = time();
        $session->userId = '';
        $session->token = '';
        $session->tokenFrom = strlen($tokenFrom) > 0 ? $tokenFrom : 'x-' . $group . '-token';

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
            $session->token = static::encode($user . ':' . static::decode($data['token']));
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
     * @param int $expireIn
     * @param string $group
     * @param string $tokenFrom
     * @return UserSession
     */
    public static function simulate(string $userId, int $expireIn, string $group = 'system', string $tokenFrom = ''): UserSession
    {
        $session = new static;
        $session->group = $group;
        $session->expireTime = time() + $expireIn;
        $session->userId = $userId;
        $session->tokenFrom = strlen($tokenFrom) > 0 ? $tokenFrom : 'x-' . $group . '-token';
        $session->token = static::encode(\md5(\microtime(true) . $userId . $group . $expireIn, true));
        return $session;
    }

    /**
     * 处理返回结果
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return array
     */
    public function processor(Application $application, Request $request, Response $response)
    {
        $response->setCookie('x-' . $this->group . '-token', $this->token)->httpOnly();
        $response->setCookie('x-token-group', $this->group)->httpOnly();
        return $this->jsonSerialize();
    }

    /**
     * 换表编码
     *
     * @param string $data
     * @return string
     */
    public static function encode(string $data): string
    {
        return str_replace(['=', '/', '+'], ['', '-', '_'], base64_encode($data));
    }

    /**
     * 换表解码
     *
     * @param string $data
     * @return string
     */
    public static function decode(string $data): string
    {
        return \base64_decode(str_replace(['-', '_'], ['/', '+'], $data));
    }

    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     * @throws \ReflectionException
     * @throws \suda\database\exception\SQLException
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $request = $bag->getRequest();
        $group = $request->getHeader('x-token-group', $request->getCookie('x-token-group', 'system'));
        $tokenFrom = $request->getHeader('x-token-group', $request->getCookie('x-token-group', 'x-' . $group . '-token'));
        return static::createFromRequest($request, $tokenFrom, $group, $bag->getApplication()->conf("app.debug-key", ''));
    }

    /**
     * 从响应中创建对象
     *
     * @param \suda\framework\Request $request
     * @param string $tokenFrom
     * @param string $group
     * @param string $debugKey
     * @return self
     * @throws \suda\database\exception\SQLException
     */
    public static function createFromRequest(Request $request, string $tokenFrom, string $group, string $debugKey)
    {
        $token = $request->getHeader($tokenFrom, $request->getCookie($tokenFrom, ''));
        $session = UserSession::load($token, $request->getRemoteAddr(), $group, $tokenFrom);
        if ($session->isGuest() && strlen($token) > 32) {
            if (strpos($token, 'debug:') === 0 && substr_count($token, ':') === 2) {
                list($debug, $user, $password) = \explode(':', $token, 3);
                if ($password === $debugKey && strlen($debugKey) > 0) {
                    $session = UserSession::simulate($user, 3600, $tokenFrom);
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
    public function isGuest(): bool
    {
        return $this->id <= 0 || $this->expireTime < time();
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
     * @param string $id 会话ID
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
     * @param string $group 会话组
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
     * @param string $token 会话Token
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
     * @param string $userId 用户ID
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
     * @param int $expireTime 过期时间
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
     * @param integer $beat 心跳时间
     *
     */
    public static function setBeat($beat)
    {
        static::$beat = $beat;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return int
     */
    public function getRefreshExpireTime(): int
    {
        return $this->refreshExpireTime;
    }

    /**
     * @param int $refreshExpireTime
     */
    public function setRefreshExpireTime(int $refreshExpireTime): void
    {
        $this->refreshExpireTime = $refreshExpireTime;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'user' => $this->userId,
            'token' => $this->token,
            'expire_time' => $this->expireTime,
            'refresh_token' => $this->refreshToken,
            'refresh_expire_time' => $this->refreshExpireTime,
            'group' => $this->group,
        ];
    }
}
