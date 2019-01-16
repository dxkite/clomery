<?php
namespace dxkite\user\provider;

use dxkite\support\file\File;
use dxkite\user\controller\AdminUserController;

class AdminUserProvider
{
    protected $controller;

    public function __construct()
    {
        $this->controller = new AdminUserController;
    }

    /**
     * 查看用户列表
     * 
     * @acl user.list
     * @param integer|null $page
     * @param integer $row
     * @return void
     */
    public function list(?int $page=1, int $row=10)
    {
        $list = $this->controller->list($page, $row);
        foreach ($list['rows'] as $index => $data) {
            $avatar = $data['avatar'];
            $data['avatar']=[];
            $data['avatar']['id'] =  $avatar ;
            $data['avatar']['link'] = u('support:upload', $avatar);
            $list['rows'][$index] = $data;
        }
        return $list;
    }

    /**
     * 设置用户状态
     *
     * @acl user.status
     * @param array $ids
     * @param integer $status 0 封禁 1 激活
     * @return void
     */
    public function modifyStatus(array $ids, int $status)
    {
        return $this->controller->modifyStatus($ids, $status);
    }

    /**
     * 删除用户
     *
     * @acl user.delete
     * @param array $ids
     * @return void
     */
    public function delete(array $ids)
    {
        return $this->controller->delete($ids);
    }

    /**
     * 搜索用户
     *
     * @acl user.list
     * @param string $field
     * @param string $search
     * @param integer|null $page
     * @param integer $row
     * @return void
     */
    public function search(string $field, string $search, ?int $page=1, int $row =10)
    {
        $list = $this->controller->search($field, $search, $page, $row);
        foreach ($list['rows'] as $index => $data) {
            $avatar = $data['avatar'];
            $data['avatar']=[];
            $data['avatar']['id'] =  $avatar ;
            $data['avatar']['link'] = u('support:upload', $avatar);
            $list['rows'][$index] = $data;
        }
        return $list;
    }


    /**
     * 获取用户信息
     *
     * @acl user.list
     * @param integer $userId
     * @return array|null
     */
    public function get(int $userId):?array
    {
        $data = $this->controller->get($userId);
        $avatar = $data['avatar'];
        $data['avatar']=[];
        $data['avatar']['id'] =  $avatar ;
        $data['avatar']['link'] = u('support:upload', $avatar);
        return $data;
    }

    /**
     * 添加用户
     *
     * @acl user.add
     * @param string $name
     * @param string|null $email
     * @param string|null $mobile
     * @param string $password
     * @return integer|null
     */
    public function add(string $name, ?string $email, ?string $mobile, string $password):?int
    {
        return $this->controller->add($name, $email, $mobile, $password);
    }

    /**
     * 编辑用户
     *
     * @acl user.edit
     * @param integer $id
     * @param array $sets 可以设置 name,email,mobile,password
     * @return void
     */
    public function edit(int $id, array $sets)
    {
        return $this->controller->edit($id, $sets);
    }

    /**
     * 设置头像
     *
     * @acl user.edit
     * @param integer $user
     * @param File $file
     * @return void
     */
    public function setAvatar(int $user, File $file)
    {
        return $this->controller->setAvatar($user, $file);
    }
}
