<?php


namespace clomery\main\table;


use clomery\content\table\FileTable;
use suda\application\database\creator\MySQLTableCreator;
use suda\application\database\Database;
use suda\database\exception\SQLException;

class AttachmentTable extends FileTable
{
    public function __construct()
    {
        parent::__construct('attachment');
        $cacheKey = 'auto-create-'.$this->getName();
        $cache = Database::application()->cache();
        // 避免多次重复创建表
        if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
            try {
                (new MySQLTableCreator($this->getSource()->write(), $this->getStruct()))->create();
                $cache->set($cacheKey, true, 0);
            } catch (SQLException $e) {
                Database::application()->dumpException($e);
            }
        }
    }
}