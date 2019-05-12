<?php
namespace content\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use suda\orm\struct\TableStruct;
use suda\orm\struct\TableStructAwareInterface;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * Class TagRelateData
 * @package content\article\data
 */
class TagRelateData  extends DataObject implements MethodParameterInterface, JsonSerializable, TableStructAwareInterface
{
    use RequestInputTrait;

    /**
     * 创建数据表结构
     *
     * @return TableStruct
     */
    public static function getTableStruct(): TableStruct
    {
        $struct = new TableStruct('tag_relate');
        $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('relate', 'bigint', 20)->unsigned()->key()->comment('相关对象'),
            $struct->field('tag', 'bigint', 20)->unsigned()->key()->comment('标签'),
        ]);
        return $struct;
    }
}