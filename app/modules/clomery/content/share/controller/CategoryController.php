<?php


namespace clomery\content\controller;


use suda\application\database\Table;
use clomery\content\table\CategoryTable;

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
     * @param string $slug
     * @return null|string
     * @throws \suda\database\exception\SQLException
     */
    public function getIdWithSlug(string $slug):?string {
        return $this->table->read(['id'])->where(['slug' => $slug])->field('id');
    }
}