<?php
namespace Core;

/**
 * 可回调对象
 */
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
        // 非空调用
        if ($this->caller) {
            if (count($params)) {
                $this->params=$params;
            }
            // 调用非静态接口
            if (!is_callable($this->caller) && is_array($this->caller)) {
                $this->caller[0]=new $this->caller[0];
            }
            return call_user_func_array($this->caller, $this->params);
        }
        return false;
    }
    public function args($vargs)
    {
        return self::call(func_get_args());
    }
}
