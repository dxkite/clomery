<?php
use archive\Query as AQuery;

class Query extends AQuery
{
    public function insert(string $table, array $values):int
    {
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

    public function where(string $table, array $wants=[], string $condithon='1', array $binds=[], bool $scroll=false):AQuery
    {
        if (count($wants)===0) {
            $fields='*';
        } else {
            $field=[];
            foreach ($wants as $want) {
                $field[]="`$want`";
            }
            $fields=implode(',', $field);
        }
        $sql='SELECT '.$fields.' FROM `'.$table.'` WHERE '.rtrim($condithon, ';').';';
        return new AQuery($sql, $binds, $scroll);
    }

    public function update(string $table, array $set_fields, string $where='1', array $binds=[]):int
    {
        $param=[];
        $count=0;
        $sets=[];
        foreach ($set_fields as $name=>$value) {
            $bname=$name.'_'.($count++);
            $sets[]="`{$name}`=:{$bname}";
            $param[$bname]=$value;
        }
        $sql='UPDATE `'.$table.'` SET '.implode(',', $sets).' WHERE ' .rtrim($where, ';').';';
        return (new Query($sql, array_merge($param, $binds)))->exec();
    }

    public function delete(string $table, string $where='1', array $binds=[]):int
    {
        $sql='DELETE FROM `'.$table.'` WHERE '.rtrim($where, ';').';';
        return (new AQuery($sql, $binds))->exec();
    }

    public function count(string $table, string $where='1', array $binds=[]):int
    {
        $sql='SELECT count(*) as `count` FROM `'.$table.'` WHERE '.rtrim($where, ';').';';
        if ($query=(new AQuery($sql, $binds))->fetch()) {
            return intval($query['count']);
        }
        return 0;
    }
}
