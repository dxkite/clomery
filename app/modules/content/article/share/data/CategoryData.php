<?php
namespace content\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use suda\orm\middleware\CommonMiddleware;
use suda\orm\middleware\Middleware;
use suda\orm\middleware\MiddlewareAwareInterface;
use suda\orm\struct\TableStruct;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * Class CategoryData
 * @package content\article\data
 */
class CategoryData  extends IndexData implements MiddlewareAwareInterface
{
    use RequestInputTrait;

    public static function getTableStruct(): TableStruct
    {
        $struct = parent::getTableStruct();
        $struct->setName('category');
        $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('name', 'varchar', 255)->comment('名称'),
            $struct->field('slug', 'varchar', 128)->unique()->comment('缩写'),
            $struct->field('image', 'varchar', 255)->comment('图标'),
            $struct->field('user', 'bigint', 20)->unsigned()->key()->comment('创建用户'),
            $struct->field('time', 'int', 11)->key()->comment('创建时间'),
            $struct->field('status', 'tinyint', 1)->key()->comment('状态'),
        ]);
        return $struct;
    }

    /**
     * @param TableStruct $struct
     * @return Middleware
     */
    public static function getMiddleware(TableStruct $struct): Middleware
    {
        $middle = new CommonMiddleware;
        $middle->serializeIt('description');
        return $middle;
    }
}

