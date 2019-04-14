<?php
namespace support\setting\controller;

use ArrayObject;
use suda\orm\TableStruct;
use support\setting\Visitor;
use support\setting\PageData;
use support\session\UserSession;
use support\openmethod\Permission;
use support\setting\table\RoleTable;
use support\setting\table\GrantTable;
use support\setting\controller\UserController;

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
     * @return \support\openmethod\Permission
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
     * @param \support\openmethod\Permission $permission 权限
     * @param integer $sort 排序
     * @return int 角色ID
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
     * @param \support\openmethod\Permission $permission
     * @param integer $sort
     * @return boolean
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
     */
    public function deleteRole(int $id):bool
    {
        return $this->role->delete(['id' => $id])->ok();
    }

    /**
     * 获取
     *
     * @param integer $id
     * @return TableStruct|null
     */
    public function getRole(int $id):?TableStruct
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
     * @return \support\setting\PageData
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
     * @param \support\session\UserSession $session
     * @return Visitor
     */
    public function getVisitor(UserSession $session):Visitor
    {
        $visitor = new Visitor($session->getUserId());
        $ctr = new VisitorController;
        $visitor->setPermission($ctr->loadPermission($session->getUserId()));
        $uCtr = new UserController;
        $data = $uCtr->getInfoById($session->getUserId());
        $visitor->setAttributes($data?$data->toArray():[]);
        return $visitor;
    }
}
