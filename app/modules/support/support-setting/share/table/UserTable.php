<?php
namespace support\setting\table;

use suda\orm\struct\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 管理员表
 */
class UserTable extends AutoCreateTable
{
    const FREEZE = 0;   // 禁用登陆
    const NORMAL = 1;  //  正常状态
    const CREATED = 1;  // 刚刚创建

    public function __construct()
    {
        parent::__construct('setting_user');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('name', 'varchar', 128)->unique()->comment('用户名'),
            $struct->field('email', 'varchar', 128)->unique()->default(null)->comment('邮箱'),
            $struct->field('mobile', 'varchar', 128)->unique()->default(null)->comment('手机号'),
            $struct->field('password', 'varchar', 255)->comment('密码'),
            $struct->field('headimg', 'varchar', 512)->comment('头像'),
            $struct->field('create_ip', 'varchar', 32)->comment('创建IP'),
            $struct->field('create_by', 'bigint', 20)->default(0)->comment('创建的用户'),
            $struct->field('create_time', 'int', 11)->key()->comment('创建时间'),
            $struct->field('status', 'tinyint', 1)->key()->default(UserTable::NORMAL)->comment('用户状态'),
        ]);
    }
}
