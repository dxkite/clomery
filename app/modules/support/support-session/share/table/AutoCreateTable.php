<?php
namespace support\session\table;

use suda\application\database\creator\MySQLTableCreator;
use suda\application\database\Database;
use suda\database\DataSource;
use suda\database\struct\TableStruct;
use suda\application\database\Table;

/**
 * Class AutoCreateTable
 * @package support\session\table
 */
abstract class AutoCreateTable extends Table
{
    /**
     * AutoCreateTable constructor.
     * @param string $name
     * @throws \suda\database\exception\SQLException
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $cacheKey = 'auto-create-'.$name;
        $cache = Database::application()->cache();
        // 避免多次重复创建表
        if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
            (new MySQLTableCreator($this->getSource()->write(), $this->getStruct()))->create();
            $cache->set($cacheKey, true, 0);
        }
    }
}
