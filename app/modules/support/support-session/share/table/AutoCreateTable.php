<?php
namespace support\session\table;

use suda\orm\DataSource;
use suda\orm\TableStruct;
use suda\application\database\Table;
use suda\orm\connection\creator\MySQLTableCreator;

abstract class AutoCreateTable extends Table
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        $cacheKey = 'auto-create-'.$name;
        $cache = Table::$application->cache();
        // 避免多次重复创建表
        if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
            (new MySQLTableCreator($this->getSource()->write(), $this->getStruct()->getFields()))->create();
            $cache->set($cacheKey, true, 0);
        }
    }
}
