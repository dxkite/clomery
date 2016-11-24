<?php
namespace server;

class Render
{
    protected $render;

    public function __construct($render)
    {
        $this->render=$render;
    }
    public function render($options)
    {
        // 页面重置
        if ($this->render instanceof \Page) {
           
            $this->render->setOptions($options)->display();
        } 
        else
        {
           (new \Page())->setOptions($options)->display($this->render);
        }
        return true;
    }

    public function setType(string $type)
    {
        $this->type=$type;
    }
}
