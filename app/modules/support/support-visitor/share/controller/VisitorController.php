<?php
namespace support\visitor\controller;


use ArrayObject;
use suda\database\exception\SQLException;
use support\openmethod\PageData;
use support\openmethod\Permission;
use support\visitor\table\GrantTable;
use support\visitor\table\RoleTable;
use support\visitor\Visitor;

class VisitorController
{
    /**
     * 授权表
     *
     * @var GrantTable
     */
    protected $grant;
    /**
     * 角色表
     *
     * @var RoleTable
     */
    protected $role;

    public function __construct()
    {
        $this->grant = new GrantTable;
        $this->role = new RoleTable;
    }

    /**
     * 加载用户权限
     *
     * @param string $userId
     * @return Permission
     * @throws SQLException
     */
    public function loadPermission(string $userId):Permission
    {
        $grantName = $this->grant->getName();
        $roleName = $this->role->getName();
        $permissions = $this->role->query("SELECT permission FROM _:{$roleName} JOIN  _:{$grantName} ON _:{$grantName}.grant = _:{$roleName}.id WHERE grantee = ? ", $userId)->all();
        if ($permissions) {
            $permission = new Permission;
            foreach ($permissions as $item) {
                $permission->merge(new Permission(json_decode($item['permission'],true)));
            }
            return $permission;
        } else {
            return new Permission;
        }
    }

    /**
     * 创建权限角色
     *
     * @param string $name 角色名
     * @param Permission $permission 权限
     * @param integer $sort 排序
     * @return int 角色ID
     * @throws SQLException
     */
    public function createRole(string $name, Permission $permission, int $sort = 0):int
    {
        if ($data = $this->role->read('id')->where(['name' => $name])->one()) {
            return $data['id'];
        }
        return $this->role->write(['name' => $name,'permission' => $permission,'sort' => $sort])->id();
    }

    /**
     * 编辑角色
     *
     * @param integer $id
     * @param string $name
     * @param Permission $permission
     * @param integer $sort
     * @return boolean
     * @throws SQLException
     */
    public function editRole(int $id, string $name, Permission $permission, int $sort = 0): bool
    {
        return $this->role->write([
            'name' => $name,
            'permission' => $permission,
            'sort' => $sort,
        ])->where(['id' => $id]) -> ok();
    }

    /**
     * 删除角色
     *
     * @param integer $id
     * @return boolean
     * @throws SQLException
     */
    public function deleteRole(int $id):bool
    {
        return $this->role->delete(['id' => $id])->ok();
    }

    /**
     * 获取
     *
     * @param integer $id
     * @return array|null
     * @throws SQLException
     */
    public function getRole(int $id):?array
    {
        return $this->role->read('*')->where(['id' => $id])->one();
    }

    /**
     * 授权
     *
     * @param integer $id 角色ID
     * @param string $grantee 权限所有者
     * @param string $investor 授权者
     * @return boolean
     * @throws SQLException
     */
    public function grant(int $id, string $grantee, ?string $investor = null): bool
    {
        if ($this->grant->read('id')->where(['grantee' => $grantee,'grant' => $id])->one()) {
            return true;
        }
        return $this->grant->write(['investor' => $investor,'grantee' => $grantee,'time' => time(),'grant' => $id])->ok();
    }

    /**
     * 收回权限
     *
     * @param integer $id
     * @param integer $grantee
     * @return boolean
     * @throws SQLException
     */
    public function revoke(int $id, int $grantee): bool
    {
        if ($data = $this->grant->read('id')->where(['grantee' => $grantee,'grant' => $id])->one()) {
            return  $this->grant->delete(['id' => $data['id']])->ok();
        }
        return false;
    }

    /**
     * 收回某个用户的全部权限
     *
     * @param integer $grantee
     * @return boolean
     * @throws SQLException
     */
    public function revokeAll(int $grantee):bool
    {
        return $this->grant->delete(['grantee' => $grantee])->ok();
    }


    /**
     * 列出角色列表
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws SQLException
     */
    public function listRole(?int $page = null, int $row = 10): PageData
    {
        return PageData::create($this->role->read('id', 'name', 'permission'), $page, $row);
    }

    /**
     * 列出角色列表
     *
     * @param string $user
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws SQLException
     */
    public function listUserRole(string $user, ?int $page = null, int $row = 10): PageData
    {
        $grants = $this->grant->read(['grant'])->where(['grantee' => $user])->all();
        if (count($grants) > 0) {
            $grantIds = [];
            foreach ($grants as $item) {
                $grantIds[] = $item['grant'];
            }
            return PageData::create($this->role->read('id', 'name', 'permission')->where(['id' => new ArrayObject($grantIds)]), $page, $row);
        }
        return PageData::empty($page, $row);
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
        $visitor = new Visitor($userId);
        $controller = new VisitorController;
        $visitor->setPermission($controller->loadPermission($userId));
        return $visitor;
    }
}
