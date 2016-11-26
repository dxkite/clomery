<?php
namespace archive;

abstract class Archive extends \helper\Value
{
    /**
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function __set(string $name, $value)
    {
        if ($this->_isField($name))
        {
            $this->var[$name]=$value;
        }
        else{
            throw new \Exception("Unknown Field $name From Table {$this->getTableName()}");
        }
    }
    // 是否为可用字段
    abstract protected function _isField($name);
    abstract public function getTableName();
}
