<?php
/**
 * Created by IntelliJ IDEA.
 * User: dxkite
 * Date: 2019/4/30 0030
 * Time: 8:19
 */

namespace clomery\article\controller;


use clomery\article\data\AttachmentData;
use clomery\article\DataUnit;
use ReflectionException;
use suda\application\database\DataAccess;
use suda\orm\exception\SQLException;
use support\setting\PageData;

class AttachmentController
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
        $this->access = $unit->unit(AttachmentData::class);
        $this->unit = $unit;
    }

    /**
     * 保存附件信息
     * @param AttachmentData $data
     * @return AttachmentData
     * @throws ReflectionException
     */
    public function save(AttachmentData $data) {
        if ($row = $this->access->read(['id'])->where(['slug' => $data['slug']])->one()) {
            $data['id']  = $row['id'];
            $this->access->write($data)->where(['id' => $row['id']])->ok();
            return $data;
        }
        $data['id'] = $this->access->write($data)->id();
        return $data;
    }

    /**
     * 获取附件列表
     * @param string $article
     * @param int $page
     * @param int $row
     * @return PageData
     * @throws SQLException
     */
    public function list(string $article, int $page = 1, int $row = 10) {
        return PageData::create($this->access->read(['id', 'path', 'hash'])->where(['article' => $article]), $page, $row);
    }
}