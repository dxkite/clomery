<?php
namespace dxkite\support\provider;

use dxkite\support\visitor\Permission;
use dxkite\support\visitor\GrantManager;

/**
 * 授权操作类
 */
class GrantProvider
{
    protected $grant;
    
    public function __construct() {
        $this->grant = new GrantManager;
    }
    
    /**
     * 创建角色
     *
     * @acl role.create
     * @param string $name
     * @param Permission $permission
     * @param integer $sort
     * @return void
     */
    public function createRole(string $name, Permission $permission, int $sort=0)
    {
        return $this->grant->createRole($name,$permission,$sort);
    }

    /**
     * 删除角色
     * 
     * @acl role.delete
     * @param integer $id
     * @return void
     */
    public function deleteRole(int $id) {
        return $this->grant->deleteRole($id);
    }

    /**
     * 授权
     *
     * @acl role.grant
     * @param integer $id 角色ID
     * @param integer $grantee ID
     * @return void
     */
    public function grant(int $id, int $grantee)
    {
        return $this->grant->grant($id,$grantee);
    }
    
    /**
     * 编辑角色
     *
     * @acl role.edit
     * @param integer $id
     * @param string $name
     * @param Permission $permission
     * @param integer $sort
     * @return void
     */
    public function editRole(int $id, string $name, Permission $permission, int $sort=0)
    {
        return $this->grant->editRole($id,$name,$permission,$sort);
    }

    /**
     * 收回权限
     *
     * @acl role.revoke
     * @param integer $id
     * @param integer $grantee 用户
     * @return void
     */
    public function revoke(int $id, int $grantee)
    {
        return $this->grant->revoke($id,$grantee);
    }
 
    /**
     * 收回某个用户的全部权限
     *
     * @acl role.revoke
     *
     * @param integer $grantee 用户
     * @return void
     */
    public function revokeAll(int $grantee)
    {
        return $this->grant->revokeAll($grantee);
    }
    
    /**
     * 列出角色列表
     * @acl role.list
     * @param integer $page
     * @param integer $rows
     * @return void
     */
    public function listRole(?int $page=null, int $rows=10)
    {
        return $this->grant->listRole($page,$rows);
    }
    

    /**
     * 获取用户角色
     *
     * @acl role.list
     * @param integer $user
     * @param integer|null $page
     * @param integer $rows
     * @return void
     */
    public function listUserRoles(int $user,?int $page=null, int $rows=10)
    {
        return $this->grant->listUserRoles($user,$page,$rows);
    }

    /**
     * 获取权限列表
     *
     * @acl role.list
     * @return array
     */
    public function getAllPermissions(): array {
        return Permission::readPermissions();
    }

    /**
     * 使用超级权限登陆
     * 
     * 可以用于登陆任意账号
     * @param integer $id
     * @return void
     */
    public function superSignInUserWithId(int $id)
    {
        return $this->grant->superSignInUserWithId($id); 
    }
}
