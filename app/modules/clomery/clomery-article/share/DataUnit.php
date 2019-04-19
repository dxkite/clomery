<?php
namespace clomery\article;

use suda\application\database\DataAccess;

/**
 * 数据单元处理
 */
class DataUnit
{
    /**
     * 数据单元
     *
     * @var array
     */
    protected $unit;

    public function __construct()
    {
        $this->unit = [];
    }

    /**
     * 添加数据单元
     *
     * @param string $unit
     * @return void
     */
    public function push(string $unit, string $real = null)
    {
        $this->unit[$unit] = $real;
    }

    /**
     * 获取操作类
     *
     * @param string $unit
     * @return DataAccess
     */
    public function unit(string $unit):DataAccess
    {
        if ($this->unit[$unit] === null) {
            $this->unit[$unit] = DataAccess::create($unit);
        }
        if (is_string($this->unit[$unit])) {
            $this->unit[$unit] = DataAccess::create($this->unit[$unit]);
        }
        return  $this->unit[$unit];
    }
}
