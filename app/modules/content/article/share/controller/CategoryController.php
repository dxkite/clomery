<?php
namespace dxkite\category\controller;

use content\article\data\CategoryData;
use content\article\DataUnit;
use ReflectionException;
use suda\application\database\DataAccess;
use suda\orm\exception\SQLException;


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
     * @throws ReflectionException
     * @throws SQLException
     */
    public function create(CategoryData $data) {
        if ($row = $this->access->read(['id'])->where(['slug' => $data['slug']])->one()) {
            $data['id']  = $row['id'];
            return $data;
        }
        $data['id'] = $this->access->write($data)->id();
        return $data;
    }

    /**
     * 根据简写获取ID
     * @param string $slug
     * @return null|string
     */
    public function getId(string $slug):?string {
        if ($row = $this->access->read(['id'])->where(['slug' => $slug])->one()) {
            return $row['id'];
        }
        return null;
    }
}
