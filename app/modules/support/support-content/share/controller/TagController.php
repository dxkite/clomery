<?php


namespace support\content\controller;


use suda\application\database\Table;
use suda\database\exception\SQLException;
use support\content\table\TagRelationTable;

class TagController extends CategoryController
{
    /**
     * @var Table
     */
    protected $relate;

    /**
     *
     * TagController constructor.
     * @param Table $table
     * @param Table $relate
     */
    public function __construct(Table $table, Table $relate)
    {
        parent::__construct($table);
        $this->assertSubOf($relate, new TagRelationTable($relate->getName()));
        $this->relate = $relate;
    }

    /**
     * @param array $data
     * @return array|null
     * @throws SQLException
     */
    public function save(array $data)
    {
        if ($id = $this->table->read(['id'])->where(['name' => $data['name']])->field('id')) {
            $data['id'] = $id;
            return $data;
        }
        return parent::save($data);
    }

    /**
     * @param array $data
     * @return bool
     * @throws SQLException
     */
    public function delete(array $data)
    {
        if (array_key_exists('id',$data))  {
            $this->relate->delete(['tag' =>  $data['id']])->ok();
        }else{
            $node = $this->table->read(['id'])->where($data);
            $this->relate->delete(['tag' => ['in', $node]])->ok();
        }
        return parent::delete($data);
    }

    /**
     * 关联标签
     *
     * @param string $tag
     * @param string $relate
     * @return bool
     * @throws SQLException
     */
    public function relate(string $tag, string $relate): bool
    {
        if ($this->relate->read(['id'])->where(['tag' => $tag, 'relate' => $relate])->one()) {
            return true;
        }
        if($this->relate->write(['tag' => $tag, 'relate' => $relate])->ok()) {
            $this->table->write('`count_item` = `count_item` + 1')->write(['id' => $tag])->ok();
            return true;
        }
        return false;
    }

    /**
     * @param string $relate
     * @param array $tagArray
     * @return bool
     * @throws SQLException
     */
    public function removeRelate(array $tagArray, string $relate) {
        $this->table->write('`count_item` = `count_item` - 1')->write(['id' => $tagArray])->ok();
        return $this->relate->delete(['tag' => $tagArray, 'relate' => $relate])->ok();
    }
}