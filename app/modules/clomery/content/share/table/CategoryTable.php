<?php


namespace clomery\content\table;


use suda\database\struct\TableStruct;

class CategoryTable extends TreeTable
{
    public function onCreateStruct(TableStruct $table): TableStruct
    {
        $struct = parent::onCreateStruct($table);
        $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('slug', 'varchar', 128)->unique()->comment('缩写'),
            $struct->field('description', 'text')->comment('描述'),
            $struct->field('image', 'varchar', 255)->comment('图标'),
            $struct->field('user', 'bigint', 20)->unsigned()->key()->comment('创建用户'),
            $struct->field('time', 'int', 11)->key()->comment('创建时间'),
            $struct->field('status', 'tinyint', 1)->key()->comment('状态'),
        ]);
        return $struct;
    }
}