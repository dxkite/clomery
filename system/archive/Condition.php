<?php
namespace archive;

class Condition
{
    protected $fields;
    protected $field;
    protected $condition;
    protected static $count=0;

    public function __construct()
    {
        self::$count++;
    }

    public function field(string $name)
    {
        if (!in_array($name, $fields)) {
            $this->fields[]=$name;
        }
        $this->field=$name;
        return $this;
    }

    public function in(array $values)
    {
        if (!self::isVoid()) {
            $condition[$this->$name]=['IN',$values];
        }
        return $this;
    }

    public function isVoid()
    {
        return empty($this->field);
    }

    public function eq($value)
    {
        if (!self::isVoid()) {
            $condition[$this->$name]=['=',$value];
        }
        return $this;
    }
    // less-than
    public function lt($value)
    {
        if (!self::isVoid()) {
            $condition[$this->$name]=['<',$value];
        }
        return $this;
    }
    // greater-than
    public function gt($value)
    {
        if (!self::isVoid()) {
            $condition[$this->$name]=['>',$value];
        }
        return $this;
    }
    // ascending
    public function asc()
    {
        if (!self::isVoid()) {
            $this->sort[]=[$this->field,'ASC'];
        }
        return $this;
    }
    //descending
    public function desc()
    {
        if (!self::isVoid()) {
            $this->sort[]=[$this->field,'DESC'];
        }
        return $this;
    }

    public function statement()
    {
        $values;
        $cond=[];
        
        foreach ($this->fields as $field =>$conds){
            foreach ($conds as $cond){

            }
        }
    }
}
