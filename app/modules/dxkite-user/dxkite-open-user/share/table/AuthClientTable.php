<?php
namespace dxkite\openuser\table;

use suda\orm\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 授权记录表
 */
class AuthClientTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('open_client_auth');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('appid', 'varchar', 255)->key()->comment('appid'),
            $struct->field('user', 'varchar', 255)->key()->comment('授权用户'),
            $struct->field('code', 'varchar', 255)->comment('密钥'),
            $struct->field('create_time', 'int', 11)->key()->default(null)->comment('Code创建时间'),
            $struct->field('access_token', 'varchar', 255)->key()->default(null)->comment('访问Token'),
            $struct->field('refresh_token', 'varchar', 255)->key()->default(null)->comment('刷新令牌'),
            $struct->field('expires_in', 'int', 11)->key()->default(null)->comment('Token过期时间'),
        ]);
    }
}
