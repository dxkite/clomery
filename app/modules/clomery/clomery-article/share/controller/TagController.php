<?php
namespace clomery\article\logic;

use clomery\article\DataUnit;
use clomery\article\data\TagData;
use clomery\article\data\TagRelateData;
use suda\application\database\DataAccess;

/**
 * 标签
 */
class TagController
{
    
    /**
     * 逻辑单元
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
        $this->access = $unit->unit(TagData::class);
        $this->unit = $unit;
    }

    /**
     * 保存标签
     *
     * @param TagData $data
     * @return string
     */
    public function save(TagData $data):string
    {
        $tag = $this->access->read(['id'])->where(['name' => $data['name']])->one();
        if ($tag) {
            unset($data['count']);
            unset($data['time']);
            if ($this->access->write($data)->where(['id' => $tag['id']])->ok()) {
                return $tag['id'];
            }
        } else {
            $data['time'] = $data['time'] ?? time();
            $data['count'] = 0;
            return $this->access->write($data)->id();
        }
        return 0;
    }

    /**
     * 获取标签ID
     *
     * @param string $name
     * @return string
     */
    public function getId(string $name):string
    {
        $tag = $this->access->read(['id'])->where(['name' => $data['name']])->one();
        if ($tag) {
            unset($data['count']);
            unset($data['time']);
            if ($this->access->write($data)->where(['id' => $tag['id']])->ok()) {
                return $tag['id'];
            }
        }
        return 0;
    }

    public function relate(string $tag, string $relate):bool
    {
        $unit = $this->unit->unit(TagRelateData::class);
        if ($unit->read(['id'])->where(['tag' => $tag, 'relate' => $relate])->one()) {
            return true;
        }
        return $unit->write(['tag' => $tag, 'relate' => $relate])->ok();
    }
}
