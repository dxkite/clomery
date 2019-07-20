<?php


namespace clomery\content\controller;


use suda\application\database\Table;
use clomery\content\table\CategoryTable;
use support\openmethod\PageData;

/**
 * Class CategoryController
 * @package clomery\content\controller
 */
class CategoryController extends TreeController
{
    /**
     * CategoryController constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->assertSubOf($table, new CategoryTable($table->getName()));
        parent::__construct($table);
    }

    /**
     * 根据简写获取ID
     * @param string $category
     * @param array $fields
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function get(string $category, array $fields = []):?array {
        if (is_numeric($category)) {
            $where['id'] = $category;
        } else {
            $where['slug'] = $category;
        }
        return $this->table->read($fields?:'*')->where($where)->one();
    }

    /**
     * @param int $page
     * @param int $row
     * @param array $fields
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getList(?int $page, int $row, array $fields = []) {
        return PageData::create($this->table->read($fields?:'*')->where(['status' => 1]), $page, $row);
    }

    /**
     * 获取键值对
     *
     * @param array $categoryId
     * @param array $fields
     * @return array
     * @throws \suda\database\exception\SQLException
     */
    public function getWithArray(array $categoryId, array $fields = []) {
        return $this->table->read($fields?:'*')
            ->where(['id' => new \ArrayObject($categoryId)])
            ->withKey('id')->all();
    }
}