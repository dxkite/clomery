<?php
namespace dxkite\android\collection\table;

class Collection extends \suda\archive\Table
{
    public function __construct()
    {
        parent::__construct('collection');
    }

    protected function onBuildCreator($table)
    {
        $table->fields(
            $table->field('id', 'bigint')->primary()->auto(),
            $table->field('name', 'varchar',255)->comment('设备名称'),
            $table->field('device', 'varchar',255)->comment('设备描述符'),
            $table->field('refer', 'text')->null()->comment('来源'),
            $table->field('content', 'text')->comment('访问内容'),
            $table->field('user', 'bigint')->null()->key()->comment('访问用户'),
            $table->field('ip', 'varchar', 32)->comment('IP'),
            $table->field('time', 'int', 11)->key()
        );
        return $table;
    }
}