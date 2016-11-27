<?php
namespace server;

use template\Manager as Manager;

class Page
{
    protected $values=[];
    protected $template;
    protected $type;
    protected $cache=true;
    protected $age=0;
    protected $close=false;
    protected $status=200;
    protected $id;
    protected $content;

    public function __construct(string $template=null, array $values=[])
    {
        $this->template=$template;
        $this->values=$values;
    }
    public function assign(array $values)
    {
        $this->values=array_merge($this->values, $values);
        return $this;
    }
    public function set(string $name, $value)
    {
        $this->values=core\ArrayHelper::set($this->values, $name, $value);
        return $this;
    }
    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isClose()
    {
        return $this->close;
    }

    /**
     * @param boolean $close
     */
    public function setClose($close)
    {
        $this->close = $close;
        return $this;
    }

    public function setOptions($options)
    {
        
        foreach ($options as $name=>$value) {
            $method='set'.ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function display(array $values=[])
    {

        header('X-Powered-By: DxSite/'.SITE_VERSION, true, $this->status);
        // 缓存控制
        if ($this->cache) {
            header('Cache-control: max-age=' .$this->age);
        } else {
            header('Cache-Control:no-cache');
        }
        if ($this->close) {
            header('Connection:close');
        }
        if ($this->type){
            header('Content-Type:'.mime($this->type));
        }

        if ($this->template){
            $set=[];
            if (is_array($values)){
                $set=$values;
            }
            self::renderTemplate($set);
            echo $this->content;
        }
        else if ($this->type==='json')
        {
            echo json_encode($values);
        }
        else{
            echo $this->content;
        }
    }

    protected function renderTemplate($values){
        // 合并数据
        self::assign($values);
        // 获取界面路径
        $file=Manager::viewPath($this->template);
        if (Storage::exist($file)) {
            $value['_Page']=new helper\Value($this->values);
            extract($value, EXTR_OVERWRITE);
            require_once $file;
        } else {
            trigger_error($this->template.' TPL no Find!');
        }
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
        return $this;
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
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCache()
    {
        return $this->cache;
    }

    /**
     * @param boolean $cache
     */
    public function setCache($cache)
    {
        if (is_string($cache)) {
            if ($cache==='true') {
                $this->cache = true;
            } else {
                $this->cache = false;
            }
        } else {
            $this->cache = $cache;
        }
        return $this;
    }
}
