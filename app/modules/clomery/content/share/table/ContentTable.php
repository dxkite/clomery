<?php


namespace clomery\content\table;


use suda\database\struct\TableStruct;

class ContentTable extends CategoryTable
{
    public function onCreateStruct(TableStruct $table): TableStruct
    {
        $struct = parent::onCreateStruct($table);
        $struct->fields([
            $struct->field('title', 'varchar', 255)->comment('标题'),
            $struct->field('category', 'bigint', 20)->key()->comment('分类'),
            $struct->field('views', 'int', 11)->key()->comment('阅读量'),
            $struct->field('content', 'text')->comment('内容'),
            $struct->field('password', 'varchar', 32)->comment('访问密码'),
            // 草稿|已发布|未发布|回收站
            $struct->field('status', 'tinyint', 1)->comment('发布状态'),
        ]);
        return $struct;
    }
}