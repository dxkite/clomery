<?php
namespace dxkite\tags\table;

use suda\archive\Table;

class TagsTable extends Table
{
    public function __construct($target)
    {
        if ($target instanceof Table) {
        } else {
            $target =Command::newClassInstance($target);
        }
        parent::__construct(self::parsePerfix($target->getTableName()).'tags');
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
            $table->field('name', 'varchar', 255)->unique()->comment("标签名"),
            $table->field('user', 'bigint', 20)->unsigned()->key()->comment("创建用户"),
            $table->field('time', 'int')->key()->comment("创建时间")
        );
    }
}
