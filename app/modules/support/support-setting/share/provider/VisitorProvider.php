<?php
namespace support\setting\provider;

use support\setting\Visitor;
use support\setting\PageData;
use support\session\UserSession;
use support\openmethod\Permission;
use support\setting\exception\UserException;
use support\setting\controller\UserController;
use support\setting\controller\VisitorController;
use support\setting\provider\UserSessionAwareProvider;

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
     * @param \support\session\UserSession $session
     * @return Visitor
     */
    public function getVisitor(UserSession $session):Visitor
    {
        return  $this->controller->getVisitor($session);
    }

    /**
     * 创建权限角色
     *
     * @acl setting:role.create
     * @param string $name 角色名
     * @param array $permission 权限
     * @param integer $sort 排序
     * @return integer 角色ID
     */
    public function createRole(string $name, array $permission, int $sort = 0):int
    {
        $permission = new Permission($permission);
        $this->visitor->getPermission()->assert($permission);
        return $this->controller->createRole($name, new Permission($permission), $sort);
    }

    /**
     * 编辑角色
     *
     * @acl setting:role.edit
     * @param integer $id
     * @param string $name
     * @param array $permission
     * @param integer $sort
     * @return boolean
     */
    public function editRole(int $id, string $name, array $permission, int $sort = 0): bool
    {
        $permission = new Permission($permission);
        $this->visitor->getPermission()->assert($permission);
        return $this->controller->editRole($id, $name, $permission, $sort);
    }
    
    /**
     * 删除角色
     *
     * @acl setting:role.delete
     * @param integer $id
     * @return boolean
     */
    public function deleteRole(int $id):bool
    {
        return $this->controller->deleteRole($id);
    }

    /**
     * 获取
     *
     * @acl setting:role.edit
     * @param integer $id
     * @return array|null
     */
    public function getRole(int $id):?array
    {
        return $this->controller->getRole($id);
    }

    /**
     * 授权
     *
     * @acl setting:role.grant
     * @param integer $id 角色ID
     * @param string $grantee 权限所有者
     * @return boolean
     */
    public function grant(int $id, string $grantee): bool
    {
        $this->assert($id);
        return $this->controller->grant($id, $grantee, $this->context->getVisitor()->getId());
    }

    /**
     * 收回权限
     *
     * @acl setting:role.revoke
     * @param integer $id
     * @param integer $grantee
     * @return boolean
     */
    public function revoke(int $id, int $grantee): bool
    {
        $this->assert($id);
        return $this->controller->revoke($id, $grantee);
    }
 
    /**
     * 收回某个用户的全部权限
     *
     * @acl setting:role.revoke
     * @param integer $grantee
     * @return boolean
     */
    public function revokeAll(int $grantee):bool
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
     * @return \support\setting\PageData
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
     */
    protected function assert(string $role)
    {
        $role = $this->getRole($role);
        $this->visitor->getPermission()->assert($role['permission']);
    }
}
