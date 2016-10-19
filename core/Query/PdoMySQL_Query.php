<?php
// 数据库查询方案
class Query implements Query_Interface
{
    protected static $pdo=null;
    protected static $prefix=null;
    protected $stmt=null;
    // 查询语句
    protected $query=null;
    // 模板值
    protected $values=null;
    protected $scroll=false;
    // 使用的数据库
    protected $database=null;
    protected $dbchange=false;
    protected $good=true;

    // TODO :  支持超大查询 max_allowed_packet

    public function __construct(string $query, array $binds=[], bool $scroll=false)
    {
        self::connectPdo();
        $this->query=$query;
        $this->values=$binds;
        $this->scroll=$scroll;
    }

    public function fetch(int $fetch_style = query::FETCH_ASSOC)
    {
        if ($this->stmt) {
            return $this->stmt->fetch($fetch_style);
        } else {
            if (self::lazyQuery($this->query, $this->values)) {
                return $this->stmt->fetch($fetch_style);
            }
        }
        return false;
    }

    public function fetchAll(int $fetch_style = query::FETCH_ASSOC)
    {
        if (self::lazyQuery($this->query, $this->values)) {
            return $this->stmt->fetchAll($fetch_style);
        }
        return false;
    }
    
    public function exec():int
    {
        if (self::lazyQuery($this->query, $this->values)) {
            return $this->stmt->rowCount();
        }
        return 0;
    }

    public function values(array $values)
    {
        $this->values=array_merge($this->values, $values);
        return $this;
    }

    public function query(string $query, array $array=[])
    {
        $this->query=$query;
        $this->values=$array;
        return $this;
    }
    public function use(string $name=null)
    {
        $this->database=$name;
        $this->dbchange=true;
        return $this;
    }
    // 获取错误
    public function error()
    {
        if ($this->stmt) {
            return $this->stmt->errorInfo();
        }
        return false;
    }
    public function erron():int
    {
        if ($this->stmt) {
            return $this->stmt->errorCode();
        }
        return false;
    }
    public static function lastInsertId()
    {
        return self::$pdo->lastInsertId();
    }
    protected function auto_prefix(string $query)
    {
        return preg_replace('/#{(\S+?)}/', self::$prefix.'$1', $query);
    }
    protected function lazyQuery(string $query, array $array=[])
    {
        $query=self::auto_prefix($query);
        // 调整数据表
        if ($this->database && $this->dbchange) {
            self::$pdo->query('USE '.$this->database);
            $this->dbchange=false;
        } elseif (!$this->database) {
            self::$pdo->query('USE '.conf('Database.dbname'));
            $this->database=conf('Database.dbname');
        }

        if ($this->scroll) {
            $stmt=self::$pdo->prepare($query, [PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL]);
        } else {
            $stmt=self::$pdo->prepare($query);
        }
        foreach ($array as $key=> $value) {
            $key=':'.ltrim($key, ':');
            if (is_array($value)) {
                $tmp =$value;
                $value = $tmp[0];
                $type = $tmp[1];
            } else {
                $type=is_numeric($value)?PDO::PARAM_INT:PDO::PARAM_STR;
            }
            $stmt->bindValue($key, $value, $type);
        }
        $return=$stmt->execute();
        // TODO: To Log This
        // var_dump($return,$stmt,$stmt->errorInfo());
        $this->stmt=$stmt;
        return $return;
    }
    protected function connectPdo()
    {
        if (!self::$pdo) {
            $pdo='mysql:host='.conf('Database.host', 'localhost').';charset='.conf('Database.charset', 'utf8');
            self::$prefix=conf('Database.prefix', '', '');
            try{
                self::$pdo = new PDO($pdo, conf('Database.user'), conf('Database.passwd'));
            } catch( Exception $e){
                $this->good=false;
            }
        }
    }
    public function good() :bool {
        return $this->good;
    }
    // 事务系列
    public static function beginTransaction()
    {
        self::connectPdo();
        return self::$pdo->beginTransaction();
    }
    
    public static function commit()
    {
        self::connectPdo();
        return  self::$pdo->commit();
    }

    public static function rollBack()
    {
        self::connectPdo();
        return  self::$pdo->rollBack();
    }
}
