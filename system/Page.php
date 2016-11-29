<?php

use template\Manager as Manager;

class Page
{
    protected static $values=[];
    protected static $template;
    protected static $type;
    protected static $cache=true;
    protected static $age=0;
    protected static $close=false;
    protected static $status=200;
    protected static $id;
    protected static $content;
    protected static $flush=false;

    public function __construct(string $template=null, array $values=[])
    {
        self::$template=$template;
        self::$values=$values;
    }

    public function assign(array $values)
    {
        self::$values=array_merge(self::$values, $values);
    }
    public function set(string $name, $value)
    {
        self::$values=helper\ArrayHelper::set(self::$values, $name, $value);
    }
    /**
     * @return int
     */
    public function getAge()
    {
        return self::$age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        self::$age = $age;
    }

    /**
     * @return boolean
     */
    public function isClose()
    {
        return self::$close;
    }

    /**
     * @param boolean $close
     */
    public function setClose($close)
    {
        self::$close = $close;
    }

    public function setOptions($options)
    {
        foreach ($options as $name=>$value) {
            $method='set'.ucfirst($name);
            if (method_exists('Page', $method)) {
                self::$method($value);
            }
        }
    }

    public function display($return)
    {
        header('X-Powered-By: DxSite/'.SITE_VERSION, true, self::$status);
        // 缓存控制
        if (self::$cache) {
            header('Cache-control: max-age=' .self::$age);
        } else {
            header('Cache-Control:no-cache');
        }
        if (self::$close) {
            header('Connection:close');
        }

        // if (self::$type) {
        //     header('Content-Type:'.mime(self::$type));
        // }

        if (self::$template) {
            $set=[];
            if (is_array($return)) {
                $set=$return;
            }
            self::renderTemplate($set);
            echo self::$content;
        } elseif (self::$type==='json') {
            echo json_encode($return);
        } else {
            echo self::$content;
        }
    }

    protected function renderTemplate($values)
    {
        // 合并数据
        self::assign($values);
        // 获取界面路径
        $file=Manager::viewPath(self::$template);
        if (Storage::exist($file)) {
            $value['_Page']=new helper\Value(self::$values);
            extract($value, EXTR_OVERWRITE);
            require_once $file;
        } else {
            trigger_error(self::$template.' TPL no Find!');
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return self::$template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        self::$template = $template;
    }
    /**
     * @param string $template
     */
    public function use($template)
    {
        self::$template = $template;
    }

    public function raw()
    {
        self::$template=null;
    }
    
    /**
     * @param mixed $type
     */
    public function type($type)
    {
       self::setType($type);
    }
        /**
     * @param mixed $type
     */
    public function json()
    {
        self::type('json');
    }
    /**
     * @return mixed
     */
    public function getType()
    {
        return self::$type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        header('Content-Type:'.mime($type));
        self::$type = $type;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return self::$status;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return self::$id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return self::$content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        self::$content = $content;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        self::$id = $id;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        self::$status = $status;
    }

    /**
     * @return boolean
     */
    public function isCache()
    {
        return self::$cache;
    }

    /**
     * @param boolean $cache
     */
    public function setCache($cache)
    {
        if (is_string($cache)) {
            if ($cache==='true') {
                self::$cache = true;
            } else {
                self::$cache = false;
            }
        } else {
            self::$cache = $cache;
        }
    }
    public function setFlush(bool $flush=true){
        return self::$flush=$flush;
    }
    public function getFlush(){
        return self::$flush;
    }
}
