<?php
namespace Core;

class Caller
{
    public $caller;
    public $params=[];

    public function __construct($caller, array $params=[])
    {
        $this->caller=$caller;
        $this->params=$params;
    }
    public function params(array $params)
    {
        $this->params=$params;
        return $this;
    }
    public function call(array $params=[])
    {
        if (count($params)) {
            $this->params=$params;
        }
        // 匿名
        return call_user_func_array($this->caller, $this->params);
    }
}
