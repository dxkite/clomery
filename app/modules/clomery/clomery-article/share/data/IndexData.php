<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use suda\orm\struct\TableStruct;
use suda\orm\struct\TableStructCreateInterface;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * 目录索引数据
 *
 */
class IndexData  extends DataObject implements MethodParameterInterface, JsonSerializable, TableStructCreateInterface
{
    use RequestInputTrait;

    /**
     * 创建数据表结构
     *
     * @param TableStruct $struct 父级或初始数据表结构
     * @return TableStruct
     */
    public static function createTableStruct(TableStruct $struct): TableStruct
    {
        $struct->fields([
            $struct->field('parent', 'bigint', 20)->key()->comment('父级文章'),
            $struct->field('count', 'int', 11)->key()->comment('字列表数量'),
            $struct->field('order', 'int', 11)->key()->comment('排序'),
            $struct->field('depth', 'int', 11)->key()->comment('深度'),
            $struct->field('index', 'varchar', 255)->key()->comment('搜索路径'),
        ]);
        return $struct;
    }
}

