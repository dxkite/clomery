<?php
use archive\Query as AQuery;

class Query extends AQuery
{
    public function insert(string $table, array $values):int
    {
        $table=self::$table($name);
        $bind='';
        $names='';
        foreach ($values as $name => $value) {
            $bind.=':'.$name.',';
            $names.='`'.$name.'`,';
            $param[$name]=$value;
        }
        $sql='INSERT INTO `'.$table.'` ('.trim($names, ',').') VALUES ('.trim($bind, ',').');';
        if ((new AQuery($sql, $values))->exec()) {
            return AQuery::lastInsertId();
        }
        return -1;
    }

    public function where(string $table, $wants='*', string $condithon='1', array $binds=[], array $page=[0,1] , bool $scroll=false):AQuery
    {
        $table=self::$table($name);
        return self::select($table, $wants, ' WHERE '.trim($condithon, ';').';', $binds, $page,$scroll);
    }

    public function select(string $table, $wants ='*', string $conditions, array $binds, array $page=[0,1],bool $scroll=false)
    {
        $table=self::$table($name);
        if (is_string($wants)) {
            $fields=$wants;
        } else {
            $field=[];
            foreach ($wants as $want) {
                $field[]="`$want`";
            }
            $fields=implode(',', $field);
        }
        return new AQuery('SELECT '.$fields.' FROM `'.$table.'` '.trim($conditions,';').' LIMIT '.self::page($page[0],$page[1]) .';', $binds, $scroll);
    }

    public function update(string $table, $set_fields, string $where='1', array $binds=[]):int
    {
        $table=self::$table($name);
        $param=[];
        $count=0;
        if (is_array($set_fields)) {
            $sets=[];
            foreach ($set_fields as $name=>$value) {
                $bname=$name.'_'.($count++);
                $sets[]="`{$name}`=:{$bname}";
                $param[$bname]=$value;
            }
            $sql='UPDATE `'.$table.'` SET '.implode(',', $sets).' WHERE ' .rtrim($where, ';').';';
        } else {
            $sql='UPDATE `'.$table.'` SET '.$set_fields.' WHERE ' .rtrim($where, ';').';';
        }
        
        return (new Query($sql, array_merge($param, $binds)))->exec();
    }

    public function delete(string $table, string $where='1', array $binds=[]):int
    {
        $table=self::$table($name);
        $sql='DELETE FROM `'.$table.'` WHERE '.rtrim($where, ';').';';
        return (new AQuery($sql, $binds))->exec();
    }

    public function count(string $table, string $where='1', array $binds=[]):int
    {
        $table=self::$table($name);
        $sql='SELECT count(*) as `count` FROM `'.$table.'` WHERE '.rtrim($where, ';').';';
        if ($query=(new AQuery($sql, $binds))->fetch()) {
            return intval($query['count']);
        }
        return 0;
    }
    protected function table(string $name)
    {
        return conf('db.perfix', '').$name;
    }

    protected function page(int $page=0, int $percount=1)
    {
        if ($percount<1) {
            $percount=1;
        }
        if ($page < 1) {
            $page = 1;
        }
        return ((intval($page) - 1) * intval($percount)) . ', ' . intval($percount);
    }
}
