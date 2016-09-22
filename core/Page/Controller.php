<?php
use Core\Caller;
use Core\Arr;

class Page_Controller extends Caller
{
    private $name;
    private $url;
    private $regs=[];
    private $tpl='index';
    private $type='html';
    private $raw=false;
    private $override=null;

    public function __construct($caller, array $params=[])
    {
        // 设置父类
        parent::__construct($caller, $params);
    }
    // 最后参数覆盖
    public function isOverride()
    {
        return $this->override;
    }
    public function override(bool $set=true)
    {
        $this->override=$set;
        return $this;
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
            Page::name($name, $this->url);
            return $this; // 链式调用
        }
        return $this->name;
    }
    // 获取/设置 标识
    public function url(string $url=null)
    {
        if ($url) {
            $this->url=$url;
            return $this; // 链式调用
        }
        return $this->url;
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
    public function type($type='')
    {
        $this->type=$type;
        return $this;
    }
    public function raw(bool $raw=true)
    {
        $this->raw=$raw;
        return $this;
    }
    public function json()
    {
        return $this->raw()->type('json');
    }
    public function render(array $value=[])
    {
        if ($this->raw) {
            switch ($this->type) {
                case 'json':
                    header('Content-type: '.mime($this->type, 'text/plain;charset=UTF-8'));
                    echo json_encode($value);
                    break;
                default:
                    header('Content-type: '.mime($this->type, 'text/plain;charset=UTF-8'));
            }
        } else {
            View::render($this->tpl, $value);
        }
    }
}
