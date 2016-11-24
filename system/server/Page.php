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
    
    public function __construct(string $template=null, array $values=null)
    {
        $this->template=$template;
        $this->values=$values;
    }
    public function assign(array $values)
    {
        self::$values=array_merge(self::$values, $values);
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
            $method='set'+ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function display($values)
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
        
        if ($this->template){
            self::renderTemplate($values);
        }
    }

    protected function renderTemplate($values){
        // 合并数据
        self::assign($values);
        // 获取界面路径
        $file=Manager::viewPath($page);
        if (Storage::exist($file)) {
            $value['_Page']=new server\core\Value($this->values);
            extract($value, EXTR_OVERWRITE);
            require_once $file;
        } elseif ($page!=='') {
            trigger_error($page.' TPL no Find!');
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
