<?php


namespace clomery\content\controller;


use clomery\content\table\FileTable;
use SplFileObject;
use suda\application\database\Table;
use suda\database\exception\SQLException;
use support\openmethod\parameter\File;


/**
 * Class FileController
 * @package clomery\content\controller
 */
class FileController extends BaseController
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
        $this->assertSubOf($table, new FileTable($table->getName()));
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
        if ($id = $this->table->read(['id'])->where(['hash' => $data['hash']])->field('id')) {
            $data['id'] = $id;
            $data['update_time'] = time();
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
     * 关联文件
     *
     * @param SplFileObject $file
     * @param string $name
     * @param string $originalName
     * @param string $uri
     * @param string $relate
     * @return bool
     * @throws SQLException
     */
    public function saveFile(File $file, string $name, string $uri, string $hash, string $relate)
    {
        $data = [];
        $data['name'] = $name;
        $data['original_name'] = $file->getOriginalName();
        $data['type'] = pathinfo($file->getOriginalName(), PATHINFO_EXTENSION);
        $data['size'] = $file->getSize();
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['uri'] = $uri;
        $data['hash'] = $hash;
        $saveData = self::save($data);
        if ($saveData !== null) {
            $this->relationController->relate($saveData['id'], $relate);
            return true;
        }
        return false;
    }
}