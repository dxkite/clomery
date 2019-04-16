<?php
namespace dxkite\openclient\provider;

use suda\orm\TableStruct;
use suda\framework\Config;
use support\setting\PageData;
use suda\application\Resource;
use dxkite\openclient\HTTPUtil;
use support\session\UserSession;
use support\openmethod\parameter\File;
use dxkite\openclient\controller\UserController;
use dxkite\openclient\provider\VisitorAwareProvider;

class UserProvider extends VisitorAwareProvider
{
    /**
     * UserController
     *
     * @var UserController
     */
    protected $controller;

    /**
     * 获取配置信息
     *
     * @var array
     */
    protected $config;

 

    public function __construct()
    {
        $this->controller = new UserController;
        $this->prepareConfig();
    }

    /**
     * 登陆
     *
     * @param-source GET
     * @param string $redirect_uri
     * @return void
     */
    public function signin(string $redirect_uri)
    {
        if ($this->visitor->isGuest()) {
            $this->config['server'];
            $redirect_uri = $this->application->getUribase($this->request).$this->getUrl('user', ['redirect_uri' => $redirect_uri, '_method' => 'authorize' ]);
            $url = $this->prepareUrl('signin', [
                'server' => $this->config['server'],
                'redirect_uri' => $redirect_uri,
                'appid' => $this->config['appid']
            ]);
            $this->response->redirect($url);
        } else {
            $this->response->redirect($redirect_uri);
        }
    }

    /**
     * 登陆
     * @param-source GET
     * @param string $redirect_uri
     * @return \support\session\UserSession
     */
    public function authorize(string $redirect_uri, string $code, string $state): UserSession
    {
        $url = $this->prepareUrl('access_token', [
            'server' => $this->config['server'],
            'secret' => $this->config['secret'],
            'code' => $code,
            'appid' => $this->config['appid']
        ]);
        $data = HTTPUtil::get($url);
        if (\array_key_exists('result', $data)) {
            $data = $data['result'];
            $userId = $this->controller->signin(
                $data['user'],
                $data['access_token'],
                $data['refresh_token'],
                $data['expires_in'],
                $this->request->getRemoteAddr()
            );
            if ($this->controller->wantUserInfo($data['user'])) {
                $userinfo = $this->prepareUrl('userinfo', [
                    'server' => $this->config['server'],
                    'user' => $data['user'] ,
                    'access_token' => $data['access_token']
                ]);
                $userinfo_data = HTTPUtil::get($userinfo);
                if (\array_key_exists('result', $userinfo_data)) {
                    $userinfo_data = $userinfo_data['result'];
                    $this->controller->edit($data['user'], $userinfo_data['name'], $userinfo_data['headimg']);
                }
            }
            $this->session = UserSession::save($userId, $this->request->getRemoteAddr(), $data['expires_in'], $this->group);
        }
        $this->response->redirect($redirect_uri);
        return $this->session;
    }

    /**
     * 退出登陆
     *
     * @param string $user
     * @return void
     */
    public function signout(string $redirect_uri)
    {
        UserSession::expire($this->visitor->getId(), $this->group);
        $this->response->redirect($redirect_uri);
    }
    
    /**
     * 获取当前用户信息
     *
     * @return array|null
     */
    public function userinfo():?array
    {
        $data = $this->controller->getInfoById($this->visitor->getId());
        return $data;
    }


    protected function prepareConfig()
    {
        $resource = new Resource(SUDA_DATA);
        $path = $resource->getConfigResourcePath('config/open-user');
        if ($path) {
            $this->config = Config::loadConfig($path);
        } else {
            $this->config = [];
        }
    }
    
    protected function prepareUrl(string $name, array $parameter)
    {
        $url = $this->config[$name];
        $keys = [];
        foreach ($parameter as $key => $value) {
            $keys[] = '{'.$key.'}';
        }
        return \str_replace($keys, \array_values($parameter), $url);
    }
}
