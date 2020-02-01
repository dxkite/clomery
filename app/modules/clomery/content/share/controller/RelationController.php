<?php


namespace clomery\content\controller;


use suda\application\database\Table;
use suda\database\exception\SQLException;
use clomery\content\table\RelationTable;

class RelationController extends BaseController
{

    /**
     *
     * TagController constructor.
     * @param Table $table
     * @throws SQLException
     */
    public function __construct(Table $table)
    {
        parent::__construct($table);
        $this->assertSubOf($table, new RelationTable($table->getName()));
    }

    /**
     * 关联标签
     *
     * @param string $item
     * @param string $relate
     * @return bool
     * @throws SQLException
     */
    public function relate(string $item, string $relate): bool
    {
        if ($this->table->read(['id'])->where(['item' => $item, 'relate' => $relate])->one()) {
            return false;
        }
        if($this->table->write(['item' => $item, 'relate' => $relate])->ok()) {
            return true;
        }
        return false;
    }

    /**
     * @param $relate
     * @param string $item
     * @return bool
     * @throws SQLException
     */
    public function remove($item, string $relate) {
        return $this->table->delete(['relate' => $relate, 'item' => $item])->ok();
    }

    /**
     * @param $relate
     * @return bool
     * @throws SQLException
     */
    public function removeRelate($relate) {
        return $this->table->delete(['relate' => $relate])->ok();
    }

    /**
     * 删除了对象
     *
     * @param $item
     * @return bool
     * @throws SQLException
     */
    public function removeItem($item) {
        return $this->table->delete(['item' => $item])->ok();
    }
}