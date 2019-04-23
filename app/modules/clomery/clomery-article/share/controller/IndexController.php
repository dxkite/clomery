<?php
namespace clomery\article\controller;

use clomery\article\DataUnit;
use suda\orm\statement\Statement;
use clomery\article\data\IndexData;
use suda\application\database\DataAccess;
use suda\framework\arrayobject\ArrayDotAccess;

/**
 * 标签
 */
class IndexController
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
        $this->access = $unit->unit(IndexData::class);
        $this->unit = $unit;
    }

    /**
     * 创建元素
     *
     * @param string $id
     * @param string $parent
     * @param integer $order
     * @return boolean
     */
    public function addItem(string $id, string $parent = '', int $order = 0):bool
    {
        $index = '0';
        $count = '0';
        if ($parentNode = $this->access->read(['parent','index', 'count', 'order'])->where(['id' => $parent])->one()) {
            $index = $parentNode['index'].$parent.'.';
            $this->access->write(['count' => $parentNode['count'] + 1])->where(['id' => $parent])->ok();
        } else {
            $index = '.';
        }
        return $this->access->write([
            'index' => $index,
            'count' => $count,
            'parent' => $parent,
            'order' => $order,
        ])->where(['id' => $id])->ok();
    }

    /**
     * 删除节点元素
     *
     * @param string $id
     * @return boolean
     */
    public function removeItem(string $id):bool
    {
        $node = $this->access->read(['parent','index', 'count', 'order'])->where(['id' => $id])->one();
        $this->access->write('`count` = `count` - 1')->where(['id' => $node['parent']])->ok();
        return $this->access->delete(['id' => $id])->ok();
    }

    /**
     * 节点树
     *
     * @param array $attribute
     * @param string $parent
     * @param string $item
     * @param array $where
     * @return array
     */
    public function node(array $attribute, string $parent = '', string $item = 'node', array $where = []):array
    {
        $index = '.';
        if ($parent !== '.' && \strlen($parent) !== 0) {
            if ($parentNode = $this->access->read(['index'])->where(['id' => $parent])->one()) {
                $index = $parentNode['index'].$parent;
            }
        }
        $attribute = array_unique(array_merge($attribute, ['index', 'count', 'order']));
        $where['index'] = ['like', $index.'%'];
        $statement = $this->access->read($attribute)->where($where);
        return $this->buildNode($index, $item, $statement);
    }

    /**
     * 构建节点
     *
     * @param string $index 起始值
     * @param string $item 子节点名称
     * @param \suda\orm\statement\Statement $statement
     * @return array
     */
    public function buildNode(string $index, string $item, Statement $statement):array
    {
        $nodes = [];
        $statement->scroll();
        while ($node = $this->access->run($statement->wantOne())) {
            $nodeIndex = trim(substr($node['index'], strlen($index)), '.');
            if (\strlen($nodeIndex) == 0) {
                $nodes[$node['id']] = $node->toArray();
            } else {
                $nodeItemIndex = \implode('.'.$item.'.', \explode('.', $nodeIndex)).'.'.$item.'.'.$node['id'];
                $nodeItemIndex = \preg_replace('/-+/', '-', $nodeItemIndex);
                ArrayDotAccess::set($nodes, $nodeItemIndex, $node->toArray());
            }
        }
        return $nodes;
    }
}
