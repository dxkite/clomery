<?php
namespace dxkite\article\table;

use suda\archive\Table;

abstract class PrefixTable extends Table
{

    public function __construct(string $prefix='', string $name)
    {
        parent::__construct(self::parsePrefix($prefix). $name);
    }

    public static function parsePrefix(string $fix)
    {
        if (!is_null($fix)) {
            $fix = $fix.'_';
            return ltrim(preg_replace('/[^\w]+/', '_', $fix), '_');
        }
        return '';
    }
}
