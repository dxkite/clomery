<?php
namespace server;

class Render
{
    protected $render;
    protected $type;
    protected $template;

    public function __construct($render)
    {
        $this->render=$render;
    }
    public function render($options)
    {
        // 页面重置
        if ($this->render instanceof Page) {
            $this->render->setOptions($options)->display();
        } 
        
        return true;
    }

    public function setType(string $type)
    {
        $this->type=$type;
    }
}
