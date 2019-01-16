<?php

namespace dxkite\support\visitor;

use dxkite\support\view\TablePager;
use dxkite\support\proxy\ProxyObject;
use dxkite\support\table\visitor\RoleTable;
use dxkite\support\table\visitor\GrantTable;

class GrantManager extends ProxyObject
{
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
        if ((new RoleTable)->select('id', ['name'=>$name])->fetch()) {
            return false;
        }
        return (new RoleTable)->insert(['name'=>$name,'permission'=>$permission,'sort'=>$sort]);
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
        if ((new GrantTable)->select('id', ['grantee'=>$grantee,'grant'=>$id])->fetch()) {
            return true;
        }
        return (new GrantTable)->insert(['investor'=>$this->getUserId(),'grantee'=>$grantee,'time'=>time(),'grant'=>$id]);
    }
    
    /**
     * 编辑角色
     *
     * @acl role.edit
     * @param integer $id
     * @param string $name
     * @param Permission $permisson
     * @param integer $sort
     * @return void
     */
    public function editRole(int $id, string $name, Permission $permisson, int $sort=0)
    {
        $id=(new RoleTable)->updateByPrimaryKey($id, [
            'name'=>$name,
            'permission'=>  $permisson,
            'sort'=>$sort,
        ]);
        return $id;
    }


    /**
     * 删除角色
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteRole(int $id):bool {
        return (new RoleTable)->deleteByPrimaryKey($id);
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
        if ($get=(new GrantTable)->select('id', ['grantee'=>$grantee,'grant'=>$id])->fetch()) {
            return  (new GrantTable)->deleteByPrimaryKey($get['id']);
        }
        return false;
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
        return (new GrantTable)->delete(['grantee'=>$grantee]);
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
        return TablePager::listWhere((new RoleTable)->setFields(['id','name','permission']), '1', [], $page, $rows);
    }
    
    public function superSignInUserWithId(int $id)
    {
        if (conf('app.debugSupport', false) && conf('app.debugSupport') === request()->getHeader('Debug-Support')) {
            return visitor()->signin($id);
        } else {
            return false;
        }
    }

    public function listUserRoles(int $userId, ?int $page=1, int $rows= 10):?array
    {
        $grants= (new GrantTable)->select(['grant'], ['grantee'=>$userId])->fetchAll();
        if (is_array($grants)) {
            foreach ($grants as $item) {
                $grantIds[]=$item['grant'];
            }
            return TablePager::listWhere((new RoleTable)->setFields(['id','name','permission']), ['id'=>$grantIds], [], $page, $rows);
        }
        return null;
    }
}
