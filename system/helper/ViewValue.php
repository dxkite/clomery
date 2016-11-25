<?php
namespace helper;

class ViewValue extends Value
{
    public function __get(string $name)
    {
        $value=parent::__get($name);
        return is_null($value)?"[:{$name}:]":$value;
    }
    
    public function __call(string $name, $args)
    {
        // 获取值
        $value=parent::__get($name);
        $args[0]=is_null($value)?(isset($args[0])?$args[0]:"[:{$name}:]"):$value;
        return parent::__call($name, $args);
    }
}
