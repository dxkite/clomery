<?php


namespace clomery\content\controller;


use suda\application\database\Table;
use suda\database\exception\SQLException;
use clomery\content\table\RelationTable;

class TagController extends CategoryController
{
    /**
     * @var RelationController
     */
    protected $relationController;

    /**
     *
     * TagController constructor.
     * @param Table $table
     * @param Table $relate
     */
    public function __construct(Table $table, Table $relate)
    {
        parent::__construct($table);
        $this->relationController = new RelationController($relate);
    }

    /**
     * @return RelationController
     */
    public function getRelationController(): RelationController
    {
        return $this->relationController;
    }

    /**
     * 创建标签
     *
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
     * 删除标签
     *
     * @param array $data
     * @return bool
     * @throws SQLException
     */
    public function delete(array $data)
    {
        if (array_key_exists('id', $data)) {
            $this->relationController->removeItem($data['id']);
        } else {
            $node = $this->table->read(['id'])->where($data);
            $this->relationController->removeItem($node);
        }
        return parent::delete($data);
    }

    /**
     * 关联标签
     *
     * @param array $tagArray
     * @param string $relate
     * @return bool
     * @throws SQLException
     */
    public function linkTag(array $tagArray, string $relate): bool
    {
        $tagUpdate = [];
        foreach ($tagArray as $index => $tag) {
            if ($this->relationController->relate($tag, $relate)) {
                $tagUpdate [] = $tag;
            }
        }
        return $this->table->write('`count_item` = `count_item` + 1')->write(['id' => new \ArrayObject($tagUpdate)])->ok();
    }

    /**
     * 移除标签
     *
     * @param string $relate
     * @param array $tagArray
     * @return bool
     * @throws SQLException
     */
    public function removeTag(array $tagArray, string $relate)
    {
        $this->table->write('`count_item` = `count_item` - 1')->write(['id' => $tagArray])->ok();
        return $this->relationController->remove(new \ArrayObject($tagArray), $relate);
    }

    /**
     * 移除所有标签
     *
     * @param $relate
     * @return bool
     * @throws SQLException
     */
    public function removeTags($relate) {
        return $this->relationController->removeRelate($relate);
    }

    /**
     * @param string $relate
     * @param array $fields
     * @return array
     * @throws SQLException
     */
    public function getTags(string $relate, array $fields = []) {
        $relate = $this->relationController->getTable()->read(['item'])->where(['relate' => $relate]);
        return $this->table->read($fields?: '*')->where(['id' => ['in', $relate]])->all();
    }
}