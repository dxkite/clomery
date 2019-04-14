<?php
namespace dxkite\openuser\table;

use suda\orm\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 单点登陆自站点
 */
class ClientTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('open_client');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('name', 'varchar', 255)->unique()->comment('网站名'),
            $struct->field('description', 'text')->default(null)->comment('站点描述'),
            $struct->field('appid', 'varchar',255)->key()->default(null)->comment('站点密钥'),
            $struct->field('secret', 'varchar',255)->default(null)->comment('站点密钥'),
            $struct->field('hostname', 'varchar',255)->default(null)->comment('站点域名限制'),
            $struct->field('access_token', 'varchar',255)->default(null)->comment('访问Token'),
            $struct->field('expires_in', 'int', 11)->default(null)->comment('过期时间'),
            $struct->field('status', 'tinyint', 1)->key()->default(1)->comment('启用状态'),    
        ]);
    }
}
