<?php
namespace support\setting\provider;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\session\UserSession;
use support\setting\VerifyImage;
use support\setting\table\UserTable;
use support\setting\exception\UserException;
use support\setting\controller\UserController;
use support\setting\controller\VisitorController;
use support\setting\provider\UserSessionAwareProvider;

class UserProvider extends UserSessionAwareProvider
{
    /**
     * UserController
     *
     * @var UserController
     */
    protected $controller;

    public function __construct()
    {
        $this->controller = new UserController;
    }

    /**
     * 登陆
     *
     * @param string $account 账号
     * @param string $password 密码
     * @param string $code 验证码
     * @param boolean $remeber 记住登陆状态7天
     * @return \support\session\UserSession 登陆会话
     */
    public function signin(string $account, string $password, string $code, bool $remeber = false): UserSession
    {
        $verify = new VerifyImage($this->context, 'support/setting');
        if ($verify->checkCode($code) === false) {
            throw new UserException('code error', UserException::ERR_CODE);
        }
        if ($user = $this->controller->signin($account, $password)) {
            $this->session = UserSession::save($user['id'], $this->request->getRemoteAddr(), $remeber ? 3600 : 25200);
            $this->context->getSession()->update();
        } else {
            throw new UserException('password or account error', UserException::ERR_PASSWORD_OR_ACCOUNT);
        }
        return $this->session;
    }

    /**
     * 退出登陆
     *
     * @param string $user
     * @return boolean
     */
    public function signout(string $user): bool {
        return UserSession::expire($user);
    }
    
    /**
     * 添加及管理员
     * 
     * @acl setting:user.add
     * @param string $name
     * @param string $password
     * @param string $ip
     * @param string|null $mobile
     * @param string|null $email
     * @param string|null $by
     * @param integer $status
     * @return string
     */
    public function add(string $name, string $password, string $ip = '', ?string $mobile = null, ?string $email = null, ?string $by = null, int $status = UserTable::NORMAL): string
    {
        return $this->controller->add($name, $password, $ip, $mobile, $email, $by, $status);
    }

    /**
     * 编辑管理员
     *
     * @acl setting:user.edit
     * @param string $id
     * @param string $name
     * @param string $password
     * @param string $ip
     * @param string|null $mobile
     * @param string|null $email
     * @param string|null $by
     * @param integer $status
     * @return boolean
     */
    public function edit(string $id, string $name, string $password, string $ip = '', ?string $mobile = null, ?string $email = null, ?string $by = null, int $status = UserTable::NORMAL): bool
    {
        return $this->controller->edit($id, $name, $password, $ip, $mobile, $email, $by, $status);
    }
    
    /**
     * 通过用户ID获取用户信息
     * 
     * @acl setting:user.edit
     * @param string $name
     * @return TableStruct|null
     */
    public function getInfoById(string $id):?TableStruct
    {
        return $this->controller->getInfoById($id);
    }

    /**
     * 列出用户
     * 
     * @acl setting:user.list
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function list(?int $page = null, int $row = 10): PageData
    {
        return $this->controller->list($page, $row);
    }

    /**
     * 搜索用户
     * 
     * @acl setting:user.list
     * @param string $data
     * @param integer|null $page
     * @param integer $row
     * @return \support\setting\PageData
     */
    public function search(string $data, ?int $page = null, int $row = 10): PageData
    {
        return $this->controller->search($data, $page, $row);
    }

    /**
     * 禁止登陆
     *
     * @acl setting:user.status
     * @param string $user
     * @return boolean
     */
    public function freeze(string $user):bool
    {
        $this->assert($user);
        return $this->controller->status($user, UserTable::FREEZE);
    }

    /**
     * 允许登陆
     *
     * @acl setting:user.status
     * @param string $user
     * @return boolean
     */
    public function active(string $user):bool
    {
        $this->assert($user);
        return $this->controller->status($user, UserTable::NORMAL);
    }

    /**
     * 删除用户
     * 
     * @acl setting:user.delete
     * @param string $user
     * @return boolean
     */
    public function delete(string $user):bool
    {
        $this->assert($user);
        return $this->controller->delete($user);
    }

    /**
     * 断言权限
     *
     * @param string $user
     * @return void
     */
    protected function assert(string $user) {
        $v = new VisitorController;
        $p = $v->loadPermission($user);
        $this->visitor->getPermission()->assert($p);
    }
}
