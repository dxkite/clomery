<?php
namespace server;

class Render
{
    protected $render;
    protected $type;

    public function __construct($render)
    {
        $this->render=$render;
    }
    public function render()
    {
        // 页面重置
        if ($this->render instanceof Page || $this->type && $this->type!=='json') {
            if (!($this->render instanceof Page)) {
                $page=new Page($this->render);
            } else {
                $page=$this->render;
            }
            return $page->display();
        } elseif (is_array($this->render)) {
            header('Content-Type:'.mime('json'));
            echo json_encode($this->render);
        }
        // 字符串原样输出
        elseif (is_string($this->render)) {
            echo $this->render;
        }
        // 可转换成JSON
        elseif ($this->render instanceof JsonSerializable) {
            echo json_encode($this->render);
        } elseif ($this->type) {
            header('Content-Type:'.mime($this->type));
        }
        return true;
    }
    
    public function setType(string $type)
    {
        $this->type=$type;
    }
}
