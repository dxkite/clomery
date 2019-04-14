<?php
namespace dxkite\openclient\table;

use suda\orm\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 链接用户表
 */
class UserTable extends AutoCreateTable
{
    const FREEZE = 0;   // 禁用登陆
    const NORMAL = 1;  //  正常状态
    const CREATED = 1;  // 刚刚创建

    const CODE_EMAIL = 1;
    const CODE_MOBILE = 2;
    
    public function __construct()
    {
        parent::__construct('open_client_user');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('name', 'varchar', 255)->default(null)->comment('用户名'),
            $struct->field('headimg', 'varchar', 512)->default(null)->comment('头像'),
            
            $struct->field('user', 'varchar', 255)->key()->comment('用户ID'),
            $struct->field('access_token', 'varchar', 255)->comment('访问密钥'),
            $struct->field('refresh_token', 'varchar', 255)->comment('刷新密钥'),
            $struct->field('expires_in', 'int', 11)->key()->comment('授权过期时间'),

            $struct->field('signup_ip', 'varchar', 32)->comment('注册IP'),
            $struct->field('signup_time', 'int', 11)->key()->comment('注册时间'),
            $struct->field('status', 'tinyint', 1)->key()->default(UserTable::NORMAL)->comment('用户状态'),
        ]);
    }
}
