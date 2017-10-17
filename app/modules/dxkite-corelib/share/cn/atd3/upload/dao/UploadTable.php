<?php
namespace cn\atd3\upload\dao;

use suda\archive\Table;
use suda\core\Query;

class UploadTable extends Table
{
    const FILE_DELETE=0;    //删除的
    const FILE_PROTECTED=1; //保护的|登陆查看，密码查看
    const FILE_PUBLIC=2;    //公开的
    const FILE_UNUSED=2;    //未使用的

    public function __construct()
    {
        parent::__construct('upload');
    }

    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('user', 'bigint', 20)->key()->unsigned(),
            $table->field('data', 'bigint', 20)->key()->unsigned()->foreign((new UploadDataTable())->getCreator()->getField('id')),
            $table->field('name', 'varchar', 255)->comment("文件名"),
            $table->field('type', 'varchar', 255)->comment("文件扩展"),
            $table->field('size', 'int', 11)->key()->comment("文件大小"),
            $table->field('time', 'int', 11)->key()->unsigned(),
            $table->field('mark', 'varchar', 80)->comment("文件标识符"),
            $table->field('visibility', 'tinyint', 1)->comment("可见性"),
            $table->field('password', 'varchar', 60)->comment("密码hash"),
            $table->field('status', 'tinyint', 1)->key()->unsigned()
        );
    }
}
