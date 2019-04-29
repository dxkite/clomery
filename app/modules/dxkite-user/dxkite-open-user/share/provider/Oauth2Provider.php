<?php
namespace dxkite\openuser\provider;

use suda\orm\TableStruct;
use support\session\UserSession;
use dxkite\openuser\table\ClientTable;
use dxkite\openuser\table\AuthClientTable;
use dxkite\openuser\controller\UserController;
use dxkite\openuser\provider\VisitorAwareProvider;
use dxkite\openuser\exception\Oauth2Exception;


/**
 * Oauth2服务提供
 */
class Oauth2Provider extends VisitorAwareProvider
{
    /**
     * 客户端
     *
     * @var ClientTable
     */
    protected $client;


    /**
     * 验证表
     *
     * @var AuthClientTable
     */
    protected $auth;

    public function __construct()
    {
        $this->client = new ClientTable;
        $this->auth = new AuthClientTable;
    }

    /**
     * 验证登陆
     *
     * @param-source GET
     * @param string $appid
     * @param string $redirect_uri
     * @param string $state
     * @param string $grant_type
     * @return void
     * @throws Oauth2Exception
     */
    public function authorize(string $appid, string $redirect_uri, string $state, string $grant_type)
    {
        if ($this->visitor->isGuest()) {
            $this->goRoute('@default:signin', ['redirect_uri' => $this->request->getUrl()]);
            return;
        }

        if ($data = $this->client->read(['id', 'hostname'])->where(['appid' => $appid, 'status' => 1])->one()) {
            $this->assertHost($redirect_uri, $data['hostname']);
            $code = $this->packToken($appid, $this->visitor->getId(), md5($appid.microtime(true), true));
            if ($this->auth->write([
                    'appid' => $appid,
                    'user' => $this->visitor->getId(),
                    'code' => $code,
                    'create_time' => time(),
                ])->ok()) {
                $this->response->redirect($this->merge($redirect_uri, ['code' => $code, 'state' => $state]));
                return;
            }
            throw new Oauth2Exception('clould not create code', Oauth2Exception::ERR_SYSTEM);
        }
        throw new Oauth2Exception('appid not available', Oauth2Exception::ERR_APPID);
    }

    /**
     * 换取AccessToken
     *
     * @param-source GET
     * @param string $appid
     * @param string $secret
     * @param string $code
     * @param string $grant_type
     * @return array
     * @throws Oauth2Exception
     */
    public function access_token(string $appid, string $secret, string $code, string $grant_type):array
    {
        if ($this->client->read(['id'])->where(['appid' => $appid, 'secret' => $secret, 'status' => 1])->one()) {
            list($appid, $user) = $this->unpackToken($code, 'invalid code', Oauth2Exception::ERR_CODE);
            if ($data = $this->auth->
                read(['id' ,'create_time'])
                ->where(['appid' => $appid, 'user' => $user, 'code' => $code])->one()) {
                if ($data['create_time'] + 300 < time()) {
                    $this->auth->delete(['id' => $data['id']]);
                    throw new Oauth2Exception('code not available', Oauth2Exception::ERR_CODE);
                }
                $access_token = $this->packToken($appid, $user, md5($appid.$secret.$code.microtime(true), true));
                $refresh_token = $this->packToken($appid, $user, md5($appid.$access_token.$secret.$code.microtime(true), true));
                if ($this->auth
                ->write(
                    [
                        'access_token' => $access_token,
                        'refresh_token' => $refresh_token,
                        'code' => null,
                        'create_time' => time(),
                        'expires_in' => 7200
                    ]
                )->where(['id' => $data['id']])->ok()) {
                    return [
                        'access_token' => $access_token,
                        'refresh_token' => $refresh_token,
                        'user' => $user,
                        'expires_in' => 7200
                    ];
                }
                throw new Oauth2Exception('clould not create access_token', Oauth2Exception::ERR_SYSTEM);
            }
            throw new Oauth2Exception('code not available', Oauth2Exception::ERR_CODE);
        }
        throw new Oauth2Exception('appid not available', Oauth2Exception::ERR_APPID);
    }

    /**
     * 刷新Token
     *
     * @param-source GET
     * @param string $appid
     * @param string $refresh_token
     * @return array
     * @throws Oauth2Exception
     */
    public function refresh_token(string $appid, string $refresh_token):array
    {
        if ($this->client->read(['id'])->where(['appid' => $appid, 'status' => 1])->one()) {
            list($appid, $user) = $this->unpackToken($refresh_token, 'invalid refresh token', Oauth2Exception::ERR_REFRESH_TOKEN);
            $access_token = $this->packToken($appid, $user, md5($appid.$refresh_token.microtime(true), true));
            $new_refresh_token = $this->packToken($appid, $user, md5($appid.$access_token.$refresh_token.microtime(true), true));
            if ($this->auth
            ->write(
                [
                    'access_token' => $access_token,
                    'refresh_token' => $new_refresh_token,
                    'code' => null,
                    'create_time' => time(),
                    'expires_in' => 7200
                ]
            )->where(['appid' => $appid, 'user' => $user , 'refresh_token' => $refresh_token])->ok()) {
                return [
                    'access_token' => $access_token,
                    'refresh_token' => $new_refresh_token,
                    'expires_in' => 7200
                ];
            }
            throw new Oauth2Exception('error refresh token', Oauth2Exception::ERR_REFRESH_TOKEN);
        }
        throw new Oauth2Exception('appid not available', Oauth2Exception::ERR_APPID);
    }

    /**
     * 获取用户信息
     *
     * @param-source GET
     * @param string $user
     * @param string $access_token
     * @return array
     * @throws Oauth2Exception
     */
    public function userinfo(string $access_token, string $user):array
    {
        $this->auth($access_token, $user);
        list($appid, $real_user) = $this->unpackToken($access_token, 'invalid access token', Oauth2Exception::ERR_ACCESS_TOKEN);
        if ($real_user !== $user) {
            throw new Oauth2Exception('invalid user id', Oauth2Exception::ERR_USER);
        }
        $controller = new UserController;
        $data = $controller->getBaseInfoById($real_user);
        if ($data === null) {
            throw new Oauth2Exception('invalid user id', Oauth2Exception::ERR_USER);
        }
        $data['headimg'] = $this->application->getUribase($this->request).'/upload/'.$data['headimg'];
        return $data;
    }

    /**
     * 验证是否有效
     *
     * @param string $access_token
     * @param string $user
     * @return bool
     * @throws Oauth2Exception
     */
    public function auth(string $access_token, string $user):bool
    {
        list($appid, $user) = $this->unpackToken($access_token, 'invalid access token', Oauth2Exception::ERR_ACCESS_TOKEN);
        if ($data = $this->auth->
                read(['id' ,'create_time' , 'expires_in'])
                ->where(['appid' => $appid, 'user' => $user, 'access_token' => $access_token])->one()) {
            if ($data['create_time'] + $data['expires_in'] > time()) {
                return true;
            } else {
                throw new Oauth2Exception('access token error', Oauth2Exception::ERR_ACCESS_TOKEN);
            }
        }
        throw new Oauth2Exception('invalid access token', Oauth2Exception::ERR_ACCESS_TOKEN);
    }

    /**
     * 合并查询参数
     *
     * @param string $target
     * @param array $parameter
     * @return string
     */
    protected function merge(string $target, array $parameter):string
    {
        if (strpos($target, '?') > 0) {
            list($target, $query) = explode('?', $target, 2);
            $value = [];
            \parse_str($query, $value);
            $parameter = array_merge($value, $parameter);
        }
        if (count($parameter) > 0) {
            return $target.'?'. \http_build_query($parameter, '_');
        }
        return $target;
    }

    /**
     * 解包Token
     * @param string $token
     * @param string $message
     * @param int $errcode
     * @return array
     * @throws Oauth2Exception
     */
    protected function unpackToken(string $token, string $message, int $errcode):array
    {
        $target = UserSession::decode($token);
        if (strpos($target, ':') === false) {
            throw new Oauth2Exception($message, $errcode);
        }
        list($appid, $user,  $token) = \explode(':', $target, 3);
        return [$appid, $user];
    }

    protected function packToken(string $appid, string $user, string $token):string
    {
        return  UserSession::encode($appid.':'.$user.':'.$token);
    }

    protected function assertHost(string $url, ?string $hostname)
    {
        $parsed = parse_url($url);
        if ($hostname !== null && $parsed['host'] !== $hostname) {
            throw new Oauth2Exception('invalid host name', Oauth2Exception::ERR_HOSTNAME);
        }
    }
}
