<?php


namespace clomery\content\table;


use suda\database\struct\TableStruct;

class TagTable extends CategoryTable
{
    public function onCreateStruct(TableStruct $table): TableStruct
    {
        $struct =  parent::onCreateStruct($table);
        $struct->fields([
            $struct->field('count_item', 'int', 11)->key()->comment('该标签元素数量'),
        ]);
        return $struct;
    }
}