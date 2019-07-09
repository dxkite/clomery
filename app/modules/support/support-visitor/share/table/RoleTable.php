<?php
namespace support\visitor\table;

use suda\database\struct\TableStruct;
use support\openmethod\Permission;
use support\session\table\AutoCreateTable;

/**
 * 管理员表
 */
class RoleTable extends AutoCreateTable
{
    const FREEZE = 0;   // 禁用登陆
    const NORMAL = 1;  //  正常状态
    const CREATED = 1;  // 刚刚创建

    public function __construct()
    {
        parent::__construct('setting_roles');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('name', 'varchar', 128)->unique()->comment('角色名'),
            $struct->field('permission', 'text')->comment('权限控制对象'),
            $struct->field('sort', 'int', 11)->key()->default(0)->comment('排序索引')
        ]);
    }

    public function _inputPermissionField($permission)
    {
        if (!($permission instanceof Permission)) {
            $permission = new Permission($permission);
        }
        return json_encode($permission->minimum());
    }

    public function _outputPermissionField($permission)
    {
        return new Permission(json_decode($permission, true));
    }
}
