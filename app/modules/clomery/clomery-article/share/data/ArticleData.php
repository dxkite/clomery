<?php
namespace clomery\article\data;

use JsonSerializable;
use suda\orm\TableStruct;
use clomery\article\Content;
use suda\orm\middleware\Middleware;
use suda\application\database\DataObject;
use suda\orm\middleware\CommonMiddleware;
use support\openmethod\RequestInputTrait;
use suda\orm\middleware\ObjectMiddlewareTrait;
use support\openmethod\MethodParameterInterface;

/**
 * 文章数据表
 */
class ArticleData extends DataObject implements MethodParameterInterface, JsonSerializable
{
    use RequestInputTrait;

    const STATUS_DELETE = 0;     // 删除
    const STATUS_DRAFT = 1;      // 草稿
    const STATUS_PUBLISH = 2;    // 发布

    public static function createStruct():TableStruct
    {
        $struct = new TableStruct('article');
        $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('user', 'bigint', 20)->unsigned()->key()->comment('作者'),
            $struct->field('title', 'varchar', 255)->key()->comment('标题'),
            $struct->field('category', 'bigint', 20)->key()->comment('文章分类'),
            $struct->field('slug', 'varchar', 255)->key()->comment('缩写'),
            $struct->field('image', 'varchar', 255)->comment('封面'),
            $struct->field('excerpt', 'text')->comment('摘要'),
            $struct->field('content', 'text')->comment('内容'),
            $struct->field('create', 'int', 11)->key()->comment('创建时间'),
            $struct->field('modify', 'int', 11)->key()->comment('修改时间'),
            $struct->field('ip', 'varchar', 32)->comment('编辑IP'),
            $struct->field('views', 'int', 11)->key()->comment('阅读量'),
            $struct->field('status', 'tinyint', 1)->key()->comment('状态'),
        ]);
        return $struct;
    }
    
    public static function createMiddleware(TableStruct $struct):Middleware
    {
        $middile = new CommonMiddleware;
        $middile->registerInput('excerpt', function ($content) {
            if (\is_string($content)) {
                return \serialize(new Content($content));
            }
            return  serialize($content);
        });
        $middile->registerInput('content', function ($content) {
            if (\is_string($content)) {
                return \serialize(new Content($content));
            }
            return  serialize($content);
        });
        $middile->registerOutput('content', function ($content) {
            return $content?unserialize($content) : new Content('');
        });
        $middile->registerOutput('excerpt', function ($content) {
            return $content?unserialize($content) : new Content('');
        });
        return $middile;
    }
}
