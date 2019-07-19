<?php


namespace clomery\content\table;


use suda\application\database\Table;
use suda\database\struct\TableStruct;

class RelationTable extends Table
{
    /**
     * 构建数据表
     * @param TableStruct $table
     * @return TableStruct
     */
    public function onCreateStruct(TableStruct $table): TableStruct
    {
        $table->fields([
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('relate', 'bigint', 20)->unsigned()->key()->comment('相关对象'),
            $table->field('item', 'bigint', 20)->unsigned()->key()->comment('目标对象'),
        ]);
        return $table;
    }
}