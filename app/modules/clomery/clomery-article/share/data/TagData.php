<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\application\database\DataObject;
use suda\orm\middleware\CommonMiddleware;
use suda\orm\middleware\Middleware;
use suda\orm\middleware\MiddlewareAwareInterface;
use suda\orm\struct\TableStruct;
use support\openmethod\RequestInputTrait;
use support\openmethod\MethodParameterInterface;


/**
 * Class TagData
 * @package clomery\article\data
 */
class TagData  extends CategoryData
{
    use RequestInputTrait;

    public static function createTableStruct(TableStruct $struct): TableStruct
    {
        $struct = parent::createTableStruct($struct);
        $struct->setName('tag');
        $struct->fields([
            $struct->field('count', 'int', 11)->key()->comment('数量'),
        ]);
        return $struct;
    }
}

