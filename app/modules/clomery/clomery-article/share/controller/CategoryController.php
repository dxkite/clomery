<?php
namespace dxkite\category\controller;

use clomery\article\data\CategoryData;
use clomery\article\DataUnit;
use suda\application\database\DataAccess;
use suda\tool\Command;
use dxkite\support\view\PageData;
use dxkite\support\view\TablePager;
use dxkite\category\table\CategoryTable;

class CategoryController
{

    /**
     * 数据单元
     *
     * @var DataUnit
     */
    protected $unit;

    /**
     * 控制器
     *
     * @var DataAccess
     */
    protected $access;


    public function __construct(DataUnit $unit)
    {
        $this->access = $unit->unit(CategoryData::class);
        $this->unit = $unit;
    }

    /**
     * 创建分类
     * @param CategoryData $data
     * @return CategoryData
     */
    public function create(CategoryData $data) {
        if ($data = $this->access->read(['id'])->where(['slug' => $data['slug']])->one()) {
            return $data;
        }
        $data['id'] = $this->access->write($data)->id();
        return $data;
    }
}
