<?php
namespace server;

class Page
{
    protected $values=[];
    protected $template;
    protected $type;
    
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
    
    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
