<?php
namespace support\visitor\provider;


use suda\database\exception\SQLException;
use support\openmethod\PageData;
use support\openmethod\Permission;
use support\visitor\controller\VisitorController;
use support\visitor\Visitor;

class VisitorProvider extends UserSessionAwareProvider
{
    /**
     * VisitorController
     *
     * @var VisitorController
     */
    protected $controller;

    public function __construct()
    {
        $this->controller = new VisitorController;
    }

    /**
     * 获取用户
     *
     * @param string $userId
     * @return Visitor
     * @throws SQLException
     */
    public function createVisitor(string $userId):Visitor
    {
        return  $this->controller->createVisitor($userId);
    }

    /**
     * 创建权限角色
     *
     * @acl setting:role.create
     * @param string $name 角色名
     * @param array $permission 权限
     * @param integer $sort 排序
     * @return integer 角色ID
     * @throws SQLException
     */
    public function createRole(string $name, array $permission, int $sort = 0):int
    {
        $permission = new Permission($permission);
        $this->visitor->getPermission()->assert($permission);
        return $this->controller->createRole($name, $permission, $sort);
    }

    /**
     * 编辑角色
     *
     * @acl setting:role.edit
     * @param string $id
     * @param string $name
     * @param array $permission
     * @param integer $sort
     * @return boolean
     * @throws SQLException
     */
    public function editRole(string $id, string $name, array $permission, int $sort = 0): bool
    {
        $permission = new Permission($permission);
        $this->visitor->getPermission()->assert($permission);
        return $this->controller->editRole($id, $name, $permission, $sort);
    }

    /**
     * 删除角色
     *
     * @acl setting:role.delete
     * @param string $id
     * @return boolean
     * @throws SQLException
     */
    public function deleteRole(string $id):bool
    {
        return $this->controller->deleteRole($id);
    }

    /**
     * 获取
     *
     * @acl setting:role.edit
     * @param string $id
     * @return array|null
     * @throws SQLException
     */
    public function getRole(string $id):?array
    {
        return $this->controller->getRole($id);
    }

    /**
     * 授权
     *
     * @acl setting:role.grant
     * @param string $id 角色ID
     * @param string $grantee 权限所有者
     * @return boolean
     * @throws SQLException
     */
    public function grant(string $id, string $grantee): bool
    {
        $this->assert($id);
        return $this->controller->grant($id, $grantee, $this->context->getVisitor()->getId());
    }


    /**
     * 数组形式授权
     * @param string $grantee
     * @param array $roles
     * @return bool
     * @throws SQLException
     */
    public function grantArray(string $grantee, array $roles) {
        $this->revokeAll($grantee);
        foreach ($roles as $id) {
            $this->grant($id, $grantee);
        }
        return true;
    }


    /**
     * 收回权限
     *
     * @acl setting:role.revoke
     * @param string $id
     * @param integer $grantee
     * @return boolean
     * @throws SQLException
     */
    public function revoke(string $id, int $grantee): bool
    {
        $this->assert($id);
        return $this->controller->revoke($id, $grantee);
    }

    /**
     * 收回某个用户的全部权限
     *
     * @acl setting:role.revoke
     * @param string $grantee
     * @return boolean
     * @throws SQLException
     */
    public function revokeAll(string $grantee):bool
    {
        return $this->controller->revokeAll($grantee);
    }


    /**
     * 列出角色列表
     *
     * @acl setting:role.list
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws SQLException
     */
    public function listRole(?int $page = null, int $row = 10): PageData
    {
        return $this->controller->listRole($page, $row);
    }

    /**
     * 列出角色列表
     *
     * @acl setting:role.list
     * @param string $user
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws SQLException
     */
    public function listUserRole(string $user, ?int $page = null, int $row = 10): PageData
    {
        return $this->controller->listUserRole($user, $page, $row);
    }

    /**
     * 断言权限
     *
     * @param string $role
     * @return void
     * @throws SQLException
     */
    protected function assert(string $role)
    {
        $role = $this->getRole($role);
        $this->visitor->getPermission()->assert($role['permission']);
    }
}
