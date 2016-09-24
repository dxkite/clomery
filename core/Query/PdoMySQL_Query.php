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


    public function __construct(string $query, array $binds=[])
    {
        self::connectPdo();
        $this->query=$query;
        $this->values=$binds;
    }

    public function fetch(int $fetch_style = query::FETCH_ASSOC)
    {
        if (self::lazyQuery($this->query, $this->values)) {
            return $this->stmt->fetch($fetch_style);
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

    public function values(array $values)
    {
        $this->$values=array_merge($this->$values, $values);
        return $this;
    }
    
    public function query(string $query, array $array=[])
    {
        $this->query=$query;
        $this->values=$array;
        return $this;
    }
    // 获取错误
    public function error()
    {
        return $this->stmt->errorInfo();
    }
    public function erron()
    {
        return $this->stmt->errorCode();
    }
    public function lastInsertId()
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
        $stmt=self::$pdo->prepare($query);
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
        $this->stmt=$stmt;
        return $return;
    }
    protected function connectPdo()
    {
        if (!self::$pdo) {
            $pdo='mysql:dbname='.conf('Database.dbname').';host='.conf('Database.host', 'localhost').';charset='.conf('Database.charset', 'utf8');
            self::$prefix=conf('Database.prefix', '', '');
            self::$pdo = new PDO($pdo, conf('Database.user'), conf('Database.passwd'));
        }
    }
}
