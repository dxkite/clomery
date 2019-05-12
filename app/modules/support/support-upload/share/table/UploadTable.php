<?php
namespace support\upload\table;

use suda\orm\struct\TableStruct;
use support\session\table\AutoCreateTable;

/**
 * 登陆日志
 */
class UploadTable extends AutoCreateTable
{
    const UPLOADING = 1; // 上传中
    const UPLOADED = 2;  // 上传完成
    const CHECKED = 3;   // 已经效验

    public function __construct()
    {
        parent::__construct('support_upload');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('user', 'bigint', 20)->key()->comment('上传用户'),
            $struct->field('name', 'varchar', 255)->default(null)->comment('文件名'),
            $struct->field('type', 'varchar', 32)->key()->default(null)->comment('扩展名'),
            $struct->field('hash', 'varchar', 32)->comment('文件HASH'),
            $struct->field('ip', 'varchar', 32)->comment('会话创建IP'),
            $struct->field('time', 'int', 11)->comment('会话创建时间'),
            $struct->field('status', 'tinyint', 11)->comment('上传状态'),
        ]);
    }
}
