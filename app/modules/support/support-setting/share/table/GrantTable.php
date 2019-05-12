<?php
namespace support\setting\table;

use suda\orm\struct\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 管理员表
 */
class GrantTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('setting_grant');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('grant', 'bigint', 20)->key()->unsigned()->comment('授权权限'),
            $struct->field('investor', 'bigint', 20)->key()->default(null)->comment('授权者'),
            $struct->field('grantee', 'bigint', 20)->key()->comment('授予者'),
            $struct->field('time', 'int', 11)->key()->comment('授予时间')
        ]);
    }
}
