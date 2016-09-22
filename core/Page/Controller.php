<?php
use Core\Caller;
use Core\Arr;

class Page_Controller extends Caller
{
    private $name;
    private $regs=[];
    private $tpl='index';
    
    public function __construct($caller, array $params=[])
    {
        // 设置父类
        parent::__construct($caller, $params);
    }
    public function preg()
    {
        return $this->regs;
    }
    // 获取匹配
    public function with($name, $preg)
    {
        if ($preg) {
            if (is_array($name) && is_array($preg)) {
                $arrs=Arr::combine($name, $preg);
                $this->regs=array_merge($arrs, $this->regs);
            } elseif (is_string($name) && is_string($preg)) {
                $this->regs[$name]=$preg;
            } else {
                trigger_error('Route::Input No Support Args Type  (please use array or string)',  E_USER_WARNING);
            }
            return $this; // 链式调用
        }
        return $this->regs[$name];
    }
    
    // 获取/设置 标识
    public function name(string $name=null)
    {
        if ($name) {
            $this->name=$name;
            return $this; // 链式调用
        }
        return $this->name;
    }
    // 获取/设置 模板
    public function template(string $name=null)
    {
        if ($name) {
            $this->tpl=$name;
            return $this; // 链式调用
        }
        return $this->tpl;
    }
    public function render()
    {
        View::render($this->tpl);
    }
}
