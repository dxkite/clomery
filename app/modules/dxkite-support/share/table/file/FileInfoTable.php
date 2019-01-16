<?php
namespace dxkite\support\table\file;

use suda\archive\Table;
use suda\core\Query;

class FileInfoTable extends Table
{
    // 可见性
    const FILE_DEFAULT=0;       // 不可访问的文件
    const FILE_PUBLISH=1;       // 公开的
    const FILE_PROTECTED=2;     // 登陆保护
    const FILE_PASSWORD=3;      // 密码保护
    const FILE_PRIVATE=4;       // 私有文件

    // 状态
    const IS_UNUSED=0;      // 未使用的
    const IS_NORMAL=1;      // 正常文件
    const IS_DELETED=2;     // 删除的
    const IS_VERIFY=3;      // 待审核文件

    public function __construct()
    {
        parent::__construct('file_info');
    }

    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('user', 'bigint', 20)->key()->unsigned(),
            $table->field('data', 'bigint', 20)->key()->unsigned()->foreign((new FileDataTable())->getCreator()->getField('id')),
            $table->field('name', 'varchar', 255)->comment("文件名"),
            $table->field('type', 'varchar', 255)->comment("文件扩展"),
            $table->field('size', 'int', 11)->key()->comment("文件大小"),
            $table->field('time', 'int', 11)->key()->unsigned(),
            $table->field('tagged', 'tinyint', 1)->key()->default(0)->comment("标记"),
            $table->field('visibility', 'tinyint', 1)->comment("可见性"),
            $table->field('password', 'varchar', 60)->comment("密码hash"),
            $table->field('status', 'tinyint', 1)->key()->unsigned()
        );
    }
}
