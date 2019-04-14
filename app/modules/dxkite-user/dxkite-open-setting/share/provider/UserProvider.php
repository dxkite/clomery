<?php
namespace dxkite\openuser\setting\provider;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\upload\UploadUtil;
use dxkite\openuser\table\UserTable;
use support\openmethod\parameter\File;
use dxkite\openuser\setting\controller\UserController;
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
     * 添加用户
     *
     * @param File|null $headimg
     * @param string $name
     * @param string $password
     * @param string|null $mobile
     * @param string|null $email
     * @param integer $status
     * @return string
     */
    public function add(?File $headimg, string $name, string $password, ?string $mobile = null, ?string $email = null, int $status = UserTable::NORMAL): string
    {
        if ($headimg !== null) {
            $img = UploadUtil::save($headimg);
        } else {
            $img = null;
        }
        return $this->controller->add($img, $name, $password, $mobile, $email, $status);
    }

    /**
     * 编辑用户
     * 
     * @acl open-user.edit
     *
     * @param string $id
     * @param File|null $headimg
     * @param string|null $name
     * @param string|null $password
     * @param string|null $mobile
     * @param string|null $email
     * @param integer $status
     * @return boolean
     */
    public function edit(string $id, ?File $headimg, ?string $name, ?string $password, ?string $mobile = null, ?string $email = null, int $status = UserTable::NORMAL): bool
    {
        if ($headimg !== null) {
            $img = UploadUtil::save($headimg);
        } else {
            $img = null;
        }
        return $this->controller->edit($id, $img, $name, $password, $mobile, $email, $status);
    }
    
    /**
     * 通过用户ID获取用户信息
     *
     * @acl open-user.edit
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
     * @acl open-user.list
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
     * @acl open-user.list
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
     * @acl open-user.status
     * @param string $user
     * @return boolean
     */
    public function freeze(string $user):bool
    {
        return $this->controller->status($user, UserTable::FREEZE);
    }

    /**
     * 允许登陆
     *
     * @acl open-user.status
     * @param string $user
     * @return boolean
     */
    public function active(string $user):bool
    {
        return $this->controller->status($user, UserTable::NORMAL);
    }

    /**
     * 删除用户
     *
     * @acl open-user.delete
     * @param string $user
     * @return boolean
     */
    public function delete(string $user):bool
    {
        return $this->controller->delete($user);
    }
}
