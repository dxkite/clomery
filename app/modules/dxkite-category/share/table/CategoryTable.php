<?php
namespace dxkite\category\table;

use suda\archive\Table;
use suda\core\Query;

class CategoryTable extends Table
{
    public function __construct($target)
    {
        if ($target instanceof Table) {
        } else {
            $target =Command::newClassInstance($target);
        }
        parent::__construct(self::parsePerfix($target->getTableName()).'category');
    }

    protected function parsePerfix(?string $fix)
    {
        if (!is_null($fix)) {
            $fix = $fix.'_';
            return ltrim(preg_replace('/[^\w]+/', '_', $fix), '_');
        }
        return '';
    }

    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('name', 'varchar', 255)->unique()->comment("分类名"),
            $table->field('slug', 'varchar', 255)->unique()->comment("分类缩写"),
            $table->field('user', 'bigint', 20)->unsigned()->key()->comment("创建用户"),
            $table->field('count', 'bigint', 20)->key()->comment("文章统计"),
            $table->field('parent', 'bigint', 20)->key()->comment("父分类")
        );
    }
}
