<?php
namespace clomery\article;

use suda\orm\DataSource;
use suda\orm\TableAccess;
use suda\orm\TableStruct;
use suda\framework\Request;
use suda\application\Application;
use suda\orm\struct\ReadStatement;
use suda\orm\middleware\Middleware;
use suda\orm\struct\WriteStatement;
use suda\application\database\Table;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;
use suda\orm\connection\creator\MySQLTableCreator;
use suda\application\database\TableMiddlewareTrait;

/**
 * 表数据对象
 * 用于对数据进行操作
 */
abstract class TableData extends TableStruct implements Middleware, MethodParameterInterface
{
    use TableMiddlewareTrait;

    /**
     * 数据操作
     *
     * @var TableAccess
     */
    protected static $access;

  
    public function __construct(string $tableName)
    {
        parent::__construct($tableName);
        $this->name = $tableName;
        $this->fields($this->defineFields()); 
    }

    public function getAccess():TableAccess
    {
        if (static::$access === null) {
            static::$access = new TableAccess($this , Table::application()->getDataSource(), $this);
            $cacheKey = 'auto-create-'.$this->name;
            $cache = Table::application()->cache();
            // 避免多次重复创建表
            if ($cache->has($cacheKey) === false && SUDA_DEBUG) {
                (new MySQLTableCreator(static::$access->getSource()->write(), $this->getFields()))->create();
                $cache->set($cacheKey, true, 0);
            }
        }
        return static::$access;
    }

    abstract public function defineFields():array;

    /**
     * 写数据
     *
     * @return \suda\orm\struct\WriteStatement
     */
    public function write(): WriteStatement
    {
        return $this->getAccess()->write($this->data);
    }

    /**
     * 读取数据
     *
     * @param array|string $fields
     * @return \suda\orm\struct\ReadStatement
     */
    public function read($fields): ReadStatement
    {
        if ($fields === null) {
            $fields = \array_keys($this->fields);
        }
        return $this->getAccess()->read($fields)->wantType(static::class);
    }

    /**
     * 统计计数
     *
     * @param string|array $where
     * @param array $whereBinder
     * @return integer
     */
    public function countIf($where, array $whereBinder):int
    {
        $field = \array_shift($fields);
        $total = $this->getAccess()->read([$field->getName()])->where($where, $whereBinder);
        $data = $this->getAccess()->query('SELECT count(*) as `count` from ('.$total.') as total', $total->getBinder())->one();
        return intval($data['count']);
    }

    /**
     * 查询语句
     *
     * @param string $query
     * @param mixed ...$parameter
     * @return QueryStatement
     */
    public function query(string $query, ...$parameter):QueryStatement
    {
        return $this->getAccess()->query($query, ...$parameter);
    }

    
    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $json = $bag->getJson();
        if ($from === 'JSON' && $json !== null && \is_array($json) && \array_key_exists($name, $json)) {
            $object = new static;
            $object->data = $json[$name];
            return $object;
        }

        if ($from === 'POST') {
            $request = $bag->getRequest();
            if ($request->hasPost($name)) {
                $data = $request->post($name);
                $object = new static;
                $object->data =  $data ?? [];
                return $object;
            }
        }
        return null;
    }
}
