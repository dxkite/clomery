<?php


namespace clomery\content\controller;


use Overtrue\Pinyin\Pinyin;
use suda\application\database\Table;
use clomery\content\table\CategoryTable;
use suda\database\exception\SQLException;
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

    public function save(array $data)
    {
        $data = $this->addIdIfUnique($data);
        if (array_key_exists('id', $data) === false) {
            $data['count_item'] = intval($data['count_item'] ?? 0);
        }
        $data['status'] = $data['status'] ?? 1;
        return parent::save($data);
    }

    /**
     * @param array $data
     * @return array
     * @throws SQLException
     */
    protected function addIdIfUnique(array $data) {
        if (array_key_exists('slug', $data)) {
            $id = $this->table->read(['id'])->where(['slug' => $data['slug']])->field('id');
            if ($id > 0 ) {
                $data['id'] = $id;
            }
        }
        return $data;
    }

    /**
     * @param string $id
     * @param int $size
     * @return bool
     * @throws SQLException
     */
    public function updateCountItem(string $id, int $size) {
        return $this->table->write('`count_item` = `count_item` + :num')
            ->addValue('num', $size)
            ->where(['id' => $id])
            ->ok();
    }

    /**
     * 根据简写获取ID
     * @param string $category
     * @param array $fields
     * @return array|null
     * @throws SQLException
     */
    public function get(string $category, array $fields = []):?array {
        $where = [];
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
     * @throws SQLException
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
     * @throws SQLException
     */
    public function getWithArray(array $categoryId, array $fields = []) {
        return $this->table->read($fields?:'*')
            ->where(['id' => new \ArrayObject($categoryId)])
            ->withKey('id')->all();
    }

    /**
     * @param array $dataArray
     * @return string
     * @throws SQLException
     */
    public function createWithOrderNameArray(array $dataArray) {
        $lastId = '';
        $pinyin = new Pinyin();
        foreach ($dataArray as $item) {
            $data = [];
            $data['name'] = $item;
            $data['slug'] = $pinyin->permalink($item, '-');
            $data['parent'] = $lastId;
            $saveData = self::save($data);
            if ($saveData !== null) {
                $lastId = $saveData['id'];
            }
        }
        return $lastId;
    }

    /**
     * @param array $dataArray
     * @return array
     * @throws SQLException
     */
    public function createWithNameArray(array $dataArray) {
        $idArray = [];
        $pinyin = new Pinyin();
        foreach ($dataArray as $item) {
            $data = [];
            $data['name'] = $item;
            $data['slug'] = $pinyin->permalink($item, '-');
            $saveData = self::save($data);
            if ($saveData !== null) {
                $idArray[] = $saveData['id'];
            }
        }
        return $idArray;
    }
}