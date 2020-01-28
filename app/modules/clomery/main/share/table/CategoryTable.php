<?php


namespace clomery\main\table;


use suda\application\database\creator\MySQLTableCreator;
use suda\application\database\Database;
use suda\database\exception\SQLException;

class CategoryTable extends \clomery\content\table\CategoryTable
{
    public function __construct()
    {
        parent::__construct('category');
        $cacheKey = 'auto-create-'.$this->getName();
        $cache = Database::application()->cache();
        // 避免多次重复创建表
        if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
            try {
                (new MySQLTableCreator())->create($this->getSource()->write(), $this->getStruct());
                $cache->set($cacheKey, true, 0);
            } catch (SQLException $e) {
                Database::application()->dumpException($e);
            }
        }
    }
}