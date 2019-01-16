<?php
namespace dxkite\tags\table;

use suda\archive\Table;

class TagTable extends Table
{
    public function __construct($target)
    {
        if ($target instanceof Table) {
        } else {
            $target =Command::newClassInstance($target);
        }
        parent::__construct(self::parsePrefix($target->getTableName()).'tags');
    }

    protected function parsePrefix(?string $fix)
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
            $table->field('ref', 'bigint', 20)->unsigned()->key(),
            $table->field('tag', 'bigint', 20)->unsigned()->key()
        );
    }
}
