<?php
namespace clomery\article\controller;

use clomery\article\DataUnit;
use function explode;
use function implode;
use function preg_replace;
use ReflectionException;
use function strlen;
use suda\orm\exception\SQLException;
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
     * @throws ReflectionException
     * @throws SQLException
     */
    public function addItem(string $id, string $parent = '', int $order = 0):bool
    {
        $count = '0';
        if ($parentNode = $this->access->read(['parent','index', 'count', 'depth', 'order'])->where(['id' => $parent])->one()) {
            $index = $parentNode['index'].$parent.'.';
            $depth = $parentNode['depth'] + 1;
            $this->access->write(['count' => $parentNode['count'] + 1])->where(['id' => $parent])->ok();
        } else {
            $index = '.';
            $depth = 1;
        }
        return $this->access->write([
            'index' => $index,
            'count' => $count,
            'parent' => $parent,
            'depth' => $depth,
            'order' => $order,
        ])->where(['id' => $id])->ok();
    }

    /**
     * 删除节点元素
     *
     * @param string $id
     * @return boolean
     * @throws ReflectionException
     * @throws SQLException
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
     * @param int|null $depth
     * @param string $item
     * @param array $where
     * @return array
     * @throws SQLException
     */
    public function node(array $attribute, string $parent = '', ?int $depth = null, string $item = 'node', array $where = []):array
    {
        $index = '.';
        if ($parent !== '.' && strlen($parent) !== 0) {
            if ($parentNode = $this->access->read(['index', 'depth'])->where(['id' => $parent])->one()) {
                $index = $parentNode['index'].$parent;
                if ($depth !== null) {
                    $depth = $parentNode['depth'] + $depth;
                }
            }
        }
        $attribute = array_unique(array_merge($attribute, ['index', 'count', 'order']));
        $where['index'] = ['like', $index.'%'];
        if ($depth !== null) {
            $where['depth'] = $depth;
        }
        $statement = $this->access->read($attribute)->where($where);
        return $this->buildNode($index, $item, $statement);
    }

    /**
     * 构建节点
     *
     * @param string $index 起始值
     * @param string $item 子节点名称
     * @param Statement $statement
     * @return array
     * @throws SQLException
     */
    public function buildNode(string $index, string $item, Statement $statement):array
    {
        $nodes = [];
        $statement->setScroll(true);
        $statement->setFetch(Statement::FETCH_ONE);
        while ($node = $this->access->run($statement)) {
            $nodeIndex = trim(substr($node['index'], strlen($index)), '.');
            if (strlen($nodeIndex) == 0) {
                $nodes[$node['id']] = $node->toArray();
            } else {
                $nodeItemIndex = implode('.'.$item.'.', explode('.', $nodeIndex)).'.'.$item.'.'.$node['id'];
                $nodeItemIndex = preg_replace('/-+/', '-', $nodeItemIndex);
                ArrayDotAccess::set($nodes, $nodeItemIndex, $node->toArray());
            }
        }
        return $nodes;
    }
}
