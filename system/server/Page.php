<?php
namespace server;

class Page
{
    protected $values=[];
    protected $template;
    public function __construct(string $template, array $values=null)
    {
        $this->template=$template;
        $this->values=$values;
    }
    public function assign(array $values)
    {
        self::$values=array_merge(self::$values, $values);
    }
    public function set(string $name, $value)
    {
        $this->values=core\ArrayHelper::set($this->values, $name, $value);
    }
    public function display(){
        
    }
}
