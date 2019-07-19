<?php


namespace clomery\content\controller;


use suda\application\database\Table;
use suda\database\exception\SQLException;
use suda\database\statement\Statement;
use suda\framework\arrayobject\ArrayDotAccess;
use clomery\content\table\TreeTable;

/**
 * Class TreeController
 * 树形结构控制器
 * @package clomery\content\controller
 */
class TreeController extends BaseController
{
    /**
     * TreeController constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->assertSubOf($table, new TreeTable($table->getName()));
        parent::__construct($table);
    }

    /**
     * @param array $data
     * @return array|null
     * @throws SQLException
     */
    public function save(array $data)
    {
        $parent = $data['parent'] ?? '';
        $order = $data['order'] ?? 0;
        $count = '0';
        if ($parentNode = $this->table->read(['parent', 'index', 'count', 'depth', 'order'])->where(['id' => $parent])->one()) {
            $index = $parentNode['index'] . $parent . '.';
            $depth = $parentNode['depth'] + 1;
            $this->table->write(['count' => $parentNode['count'] + 1])->where(['id' => $parent])->ok();
        } else {
            $index = '.';
            $depth = 1;
        }
        $data = array_merge([
            'index' => $index,
            'count' => $count,
            'parent' => $parent,
            'depth' => $depth,
            'order' => $order,
        ], $data);
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
            $parent = $this->table->read(['parent'])->where(['id' => $data['id']])->field('parent');
            $this->table->write('`count` = `count` - 1')->where(['id' => $parent])->ok();
        }else{
            $node = $this->table->read(['parent'])->where($data);
            $this->table->write('`count` = `count` - 1')->where(['id' => ['in', $node]])->ok();
        }
        return parent::delete($data);
    }

    /**
     *  获取节点树
     *
     * @param array $attribute
     * @param string $parent
     * @param int|null $depth
     * @param string $item
     * @param array $where
     * @return array
     * @throws SQLException
     */
    public function getNodeTree(array $attribute, string $parent = '', ?int $depth = null, string $item = 'node', array $where = []): array
    {
        $index = '.';
        if ($parent !== '.' && strlen($parent) !== 0) {
            if ($parentNode = $this->table->read(['index', 'depth'])->where(['id' => $parent])->one()) {
                $index = $parentNode['index'] . $parent;
                if ($depth !== null) {
                    $depth = $parentNode['depth'] + $depth;
                }
            }
        }
        $attribute = array_unique(array_merge($attribute, ['index', 'count', 'order']));
        $where['index'] = ['like', $index . '%'];
        if ($depth !== null) {
            $where['depth'] = $depth;
        }
        $statement = $this->table->read($attribute)->where($where);
        return $this->buildNodeTree($index, $item, $statement);
    }

    /**
     * 构建节点树
     *
     * @param string $index 起始值
     * @param string $item 子节点名称
     * @param Statement $statement
     * @return array
     * @throws SQLException
     */
    protected function buildNodeTree(string $index, string $item, Statement $statement): array
    {
        $nodes = [];
        $statement->setScroll(true);
        $statement->setFetch(Statement::FETCH_ONE);
        while ($node = $this->table->run($statement)) {
            $nodeIndex = trim(substr($node['index'], strlen($index)), '.');
            if (strlen($nodeIndex) == 0) {
                $nodes[$node['id']] = $node->toArray();
            } else {
                $nodeItemIndex = implode('.' . $item . '.', explode('.', $nodeIndex)) . '.' . $item . '.' . $node['id'];
                $nodeItemIndex = preg_replace('/-+/', '-', $nodeItemIndex);
                ArrayDotAccess::set($nodes, $nodeItemIndex, $node->toArray());
            }
        }
        return $nodes;
    }
}