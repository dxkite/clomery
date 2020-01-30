<?php


namespace clomery\content\table;


use suda\database\struct\TableStruct;

class ContentTable extends CategoryTable
{
    const DELETE = 0;
    const PUBLISH = 1;
    const HIDDEN = 2;
    const CRASH = 3;

    public function onCreateStruct(TableStruct $table): TableStruct
    {
        $struct = parent::onCreateStruct($table);
        $struct->fields([
            $struct->field('title', 'varchar', 255)->comment('标题'),
            $struct->field('category', 'bigint', 20)->key()->comment('分类'),
            $struct->field('views', 'int', 11)->key()->comment('阅读量'),
            $struct->field('stick', 'int', 11)->key()->comment('置顶'),
            $struct->field('content', 'text')->comment('内容'),
            $struct->field('content_hash', 'varchar', 32)->default('')->key()->comment('内容HASH'),
            $struct->field('password', 'varchar', 32)->comment('访问密码'),
            $struct->field('modify_time', 'int', 11)->key()->comment('修改时间'),
            // 草稿|已发布|未发布|回收站
            $struct->field('status', 'tinyint', 1)->comment('发布状态'),
        ]);
        return $struct;
    }
}