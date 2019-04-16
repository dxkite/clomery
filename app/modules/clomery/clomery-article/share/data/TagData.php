<?php
namespace clomery\article\data;

use clomery\article\TableData;

/**
 * 分类数据表
 */
class TagsData extends TableData
{
    
    public function __construct(string $name = null)
    {
        parent::__construct($name ?? 'tag');
    }

    public function defineFields():array
    {
        return [
            $this->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $this->field('name', 'varchar', 255)->unique()->comment("标签名"),
            $this->field('count', 'int')->comment("标签下的数量"),
            $this->field('user', 'bigint', 20)->unsigned()->key()->comment("创建用户"),
            $this->field('time', 'int')->key()->comment("创建时间")
        ];
    }
}
