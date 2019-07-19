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
            $struct->field('content', 'text')->comment('内容')
        ]);
        return $struct;
    }
}