<?php


namespace content\page\data;


use JsonSerializable;
use suda\application\database\DataObject;
use suda\orm\middleware\CommonMiddleware;
use suda\orm\middleware\Middleware;
use suda\orm\middleware\MiddlewareAwareInterface;
use suda\orm\struct\TableStruct;
use suda\orm\struct\TableStructAwareInterface;
use support\openmethod\MethodParameterInterface;
use support\openmethod\RequestInputTrait;

class PagesData extends DataObject implements MethodParameterInterface, JsonSerializable, TableStructAwareInterface, MiddlewareAwareInterface
{
    use RequestInputTrait;

    /**
     * 创建数据表结构
     *
     * @return TableStruct
     */
    public static function getTableStruct(): TableStruct
    {
        $struct = new TableStruct('page');
        $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('method', 'varchar', 128)->comment('方法'),
            $struct->field('uri', 'varchar', 255)->comment('请求URI'),
            $struct->field('template', 'varchar', 255)->comment('模板'),
            $struct->field('data', 'text')->comment('模板数据'),
            $struct->field('status', 'int', 11)->key()->default(1)->comment('状态'),
        ]);
        return $struct;
    }


    /**
     * @param TableStruct $struct
     * @return Middleware
     */
    public static function getMiddleware(TableStruct $struct): Middleware
    {
        $middleware = new CommonMiddleware();

        return $middleware;
    }
}