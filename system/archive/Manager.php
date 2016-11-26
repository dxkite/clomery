<?php
namespace archive;

/**
*   储存管理器
*/
class Manager
{
    protected $archive;
    protected $where=[];
    protected $wparam=[];
    protected $names=[];

    public function __construct(Archive $archive)
    {
        $this->archive=$archive;
    }

    public function insert()
    {
        $values=$this->archive->_getVar();
        $bind='';
        $names='';
        foreach ($values as $name => $value) {
            $bind.=':'.$name.',';
            $names.='`'.$name.'`,';
            $param[$name]=$value;
        }
        $sql='INSERT INTO `'.$this->archive->getTableName().'` ('.trim($names, ',').') VALUES ('.trim($bind, ',').');';
        if ((new Query($sql, $values))->exec()) {
            return Query::lastInsertId();
        }
        return 0;
    }
    
    public function update(array  $wants, int $limit=1)
    {
        $values=$this->archive->_getVar();
        $param=[];
        $sets=[];
        $where=[];
        foreach ($values as $name=>$value) {
            $this->names[]=$name;
            $bname=$name.'_'.count($this->names);
            if (in_array($name, $wants)) {
                $sets[]="`{$name}`=:{$bname}";
            } else {
                $where[]="`{$name}`=:{$bname}";
            }
            $param[$bname]=$value;
        }
        $sql='UPDATE `'.$this->archive->getTableName().'` SET '.implode(',', $sets).' WHERE ' .implode(' AND ', $where).' LIMIT '.$limit.';';
        return (new Query($sql, $param))->exec();
    }

    public function where($where)
    {
        $wants=[];
        if (func_num_args()>1) {
            $wants=func_get_args();
        } elseif (is_array($where)) {
            $wants[]=$where;
        } else {
            $wants[]=[$where];
        }
        $or=[];
        $names=[];
        $param=[];
        foreach ($wants as $want) {
            $and=[];
            foreach ($want as $name => $value) {
                $this->names[]=$name;
                $bname=$name.'_'.count($this->names);
                if (is_array($value) && count($value)===2) {
                    $and[]="`{$name}` {$value[0]} :{$bname}";
                    $param[$bname]=$value[1];
                } else {
                    $and[]="`{$name}`=:{$bname}";
                    $param[$bname]=$value;
                }
            }
            $or[]='('.implode(' AND ', $and).')';
        }
        $this->where[]=implode(' OR ', $or);
        $this->wparam=$param;
        return $this;
    }

    public function delete(int $limit=1)
    {
        $param=[];
        $where=[];
        $names=[];
        $values=$this->archive->_getVar();
        foreach ($values as $name=>$value) {
            $this->names[]=$name;
            $bname=$name.'_'.count($this->names);
            $where[]="`{$name}`=:{$bname}";
            $param[$bname]=$value;
        }
        $sql='DELETE FROM `'.$this->archive->getTableName().'` WHERE ' .implode(' AND ', $where). ' LIMIT '.$limit.';';
        return (new Query($sql, $param))->exec();
    }

    
}
