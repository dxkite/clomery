<?php
namespace support\content\table;


use suda\application\database\Table;
use suda\database\struct\TableStruct;

/**
 * Class TreeTable
 * 树形结构支持
 *
 * @package support\content\table
 */
class TreeTable extends Table {

    /**
     * 构建数据表
     * @param TableStruct $table
     * @return TableStruct
     */
    public function onCreateStruct(TableStruct $table): TableStruct
    {
        return $table->fields([
            $table->field('name', 'varchar', 255)->key()->comment('名称'),
            $table->field('parent', 'bigint', 20)->key()->comment('父级'),
            $table->field('count', 'int', 11)->key()->comment('字列数量'),
            $table->field('order', 'int', 11)->key()->comment('排序'),
            $table->field('depth', 'int', 11)->key()->comment('深度'),
            $table->field('index', 'varchar', 255)->key()->comment('搜索路径'),
        ]);
    }
}