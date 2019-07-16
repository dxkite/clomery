<?php


namespace support\content\controller;


use exception\ContentException;
use suda\application\database\Table;

class BaseController
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * BaseController constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        if ($table->getStruct()->hasField('id') === false) {
            throw new ContentException(get_class($table) . ' must has id');
        }
        $this->table = $table;
    }

    /**
     * @param Table $test
     * @param Table $table
     */
    protected function assertSubOf(Table $test, Table $table)
    {
        if ($test->isSubOf($table) === false) {
            throw new ContentException(get_class($test) . ' is not sub table of ' . get_class($table));
        }
    }

    /**
     * 保存
     * @param array $data
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function save(array $data)
    {
        if (array_key_exists('id',$data)) {
            if ($this->table->write($data)->where(['id' => $data['id']])->ok()) {
                return $data;
            }
        } else {
            if ($id = $this->table->write($data)->id()) {
                $data['id'] = $id;
                return $data;
            }
        }
        return null;
    }

    /**
     * 删除
     * @param array $data
     * @return bool
     * @throws \suda\database\exception\SQLException
     */
    public function delete(array $data)
    {
        if (array_key_exists('id',$data)) {
            return $this->table->delete(['id' => $data['id']])->ok();
        } else {
            return $this->table->delete($data)->ok();
        }
    }
}