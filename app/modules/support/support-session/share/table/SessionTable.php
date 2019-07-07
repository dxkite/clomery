<?php
namespace support\session\table;

use suda\database\struct\TableStruct;
use support\session\table\AutoCreateTable;

/**
 * 登陆日志
 */
class SessionTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('support_session');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('grantee', 'bigint', 20)->key()->comment('会话所有者'),
            $struct->field('group', 'varchar', 32)->key()->comment('会话分组'),
            $struct->field('token', 'varchar', 32)->key()->comment('验证令牌'),
            $struct->field('expire', 'int', 11)->key()->comment('过期时间'),
            $struct->field('refresh_token', 'varchar', 32)->key()->comment('刷新令牌'),
            $struct->field('refresh_expire', 'int', 11)->key()->comment('刷新过期时间'),
            $struct->field('ip', 'varchar', 32)->key()->comment('会话创建IP'),
            $struct->field('time', 'int', 11)->comment('会话创建时间'),
        ]);
    }
}
