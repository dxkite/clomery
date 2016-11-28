<?php
use archive\Query as AQuery;

class Query extends AQuery
{
    public function insert(string $table, $values, array $binds=[]):int
    {
        $table=self::table($table);
        if (is_string($values)) {
            $sql=$sql='INSERT INTO `'.$table.'` '.trim($values,';').' ;';
        } elseif (is_array($values)) {
            $bind='';
            $names='';
            foreach ($values as $name => $value) {
                $bind.=':'.$name.',';
                $names.='`'.$name.'`,';
                $param[$name]=$value;
            }
            $binds=$values;
            $sql='INSERT INTO `'.$table.'` ('.trim($names, ',').') VALUES ('.trim($bind, ',').');';
        }
        if ((new AQuery($sql,$binds))->exec()) {
            return AQuery::lastInsertId();
        }
        return -1;
    }

    public function where(string $table, $wants='*', $condithon='1', array $binds=[], array $page=[0, 1], bool $scroll=false):AQuery
    {
        $table=self::table($table);

        if (is_array($condithon)) {
            $count=0;
            $and=[];
            foreach ($condithon as $name => $value) {
                $bname=$name.'_'.($count++);
                $and[]="`{$name}`=:{$bname}";
                $param[$bname]=$value;
            }
            $condithon=implode(' AND ', $and);
            $binds=array_merge($binds, $param);
        }

        return self::select($table, $wants, ' WHERE '.trim($condithon, ';').';', $binds, $page, $scroll);
    }

    public function select(string $table, $wants ='*',  $conditions, array $binds, array $page=[0, 1], bool $scroll=false)
    {
        $table=self::table($table);

        
        if (is_string($wants)) {
            $fields=$wants;
        } else {
            $field=[];
            foreach ($wants as $want) {
                $field[]="`$want`";
            }
            $fields=implode(',', $field);
        }

        return new AQuery('SELECT '.$fields.' FROM `'.$table.'` '.trim($conditions, ';').' LIMIT '.self::page($page[0], $page[1]) .';', $binds, $scroll);
    }

    public function update(string $table, $set_fields,  $where='1', array $binds=[]):int
    {
        $table=self::table($table);
        $param=[];
        $count=0;
        if (is_array($where)) {
            $count=0;
            $and=[];
            foreach ($where as $name => $value) {
                $bname=$name.'_'.($count++);
                $and[]="`{$name}`=:{$bname}";
                $param[$bname]=$value;
            }
            $where=implode(' AND ', $and);
            $binds=array_merge($binds, $param);
        }
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
        $table=self::table($table);
        $sql='DELETE FROM `'.$table.'` WHERE '.rtrim($where, ';').';';
        return (new AQuery($sql, $binds))->exec();
    }

    public function count(string $table, string $where='1', array $binds=[]):int
    {
        $table=self::table($table);
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
