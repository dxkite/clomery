<?php
namespace Core;

/**
 * 可回调对象
 */
class Caller
{
    public $caller;
    public $params=[];
    // TODO : 可以引用文件的调用
    public function __construct($caller, array $params=[])
    {
        if (is_string($caller)){
            $caller= self::parseCaller($caller);
        }
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
    public static function parseCaller(string $caller)
    {
        preg_match('/^([\w\\\\]+)(?:#(\w+))?/', $caller, $matchs);
        if (isset($matchs[2])) {
            return [$matchs[1],$matchs[2]];
            $this->params=$args;
        } elseif (isset($matchs[1])) {
           return $matchs[1];
        }
        return null;
    }
}
