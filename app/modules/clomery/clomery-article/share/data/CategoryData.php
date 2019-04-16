<?php
namespace clomery\article\data;

use clomery\article\TableData;

/**
 * 分类数据表
 */
class CategoryData extends TableData
{
    
    public function __construct(string $name = null)
    {
        parent::__construct($name ?? 'category');
    }

    public function defineFields():array
    {
        return [
            $this->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $this->field('name', 'varchar', 255)->unique()->comment("分类名"),
            $this->field('slug', 'varchar', 255)->unique()->comment("分类缩写"),
            $this->field('user', 'bigint', 20)->unsigned()->key()->comment("创建用户"),
            $this->field('count', 'bigint', 20)->key()->comment("文章统计"),
            $this->field('parent', 'bigint', 20)->key()->comment("父分类")
        ];
    }
}
