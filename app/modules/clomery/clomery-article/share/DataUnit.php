<?php
namespace clomery\article;

use suda\application\Application;
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
     * @param string|null $real
     * @return void
     */
    public function push(string $unit, ?string $real = null)
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
            $this->unit[$unit] = new DataAccess($unit);
        }
        if (is_string($this->unit[$unit])) {
            $this->unit[$unit] = new DataAccess($this->unit[$unit]);
        }
        return  $this->unit[$unit];
    }

    public function init(string $unit, Application $application, ?string $real = null)
    {
        $this->push($unit, $real);
        $unit = $this->unit($unit);
        (new \suda\orm\connection\creator\MySQLTableCreator(
        $application->getDataSource()->write(),
        $unit->getStruct()->getFields()
        ))->create();
    }
}
