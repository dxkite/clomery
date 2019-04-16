<?php
namespace clomery\article\data;

use clomery\article\TableData;

/**
 * 分类数据表
 */
class TagRelationData extends TableData
{
    public function __construct(string $name = null)
    {
        parent::__construct($name ?? 'tag_relation');
    }

    public function defineFields():array
    {
        return [
            $this->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $this->field('ref', 'bigint', 20)->unsigned()->key(),
            $this->field('tag', 'bigint', 20)->unsigned()->key()
        ];
    }
}
