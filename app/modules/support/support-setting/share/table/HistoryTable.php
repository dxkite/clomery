<?php
namespace support\setting\table;

use suda\orm\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 管理员表
 */
class HistoryTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('setting_view_history');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('session', 'varchar', 32)->key()->comment('访问会话'),
            $struct->field('user', 'varchar', 20)->key()->comment('用户ID'),
            $struct->field('hash', 'varchar', 32)->key()->comment('访问地址'),
            $struct->field('ip', 'varchar', 32)->key()->comment('访问IP'),
            $struct->field('url', 'varchar', 512)->comment('访问地址'),
            $struct->field('time', 'int', 11)->key()->comment('访问时间'),
        ]);
    }
}
