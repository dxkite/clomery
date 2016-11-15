<?php
namespace Core;

use \Page;
use \View;

/**
* 页面控制器，控制页面的加载
*
*/
class PageController extends Caller
{
    /**
    * $id 页面唯一标识
    */
    private $id;
    /**
    * $url 页面访问路径匹配
    */
    private $url;
    /**
    * $regs 中对应的元素的匹配符号
    */
    private $regs=[];
    /**
    * $tpl当前页面模板
    */
    private $tpl='';
    /**
    * $type 当前页面会被呈现的Content-type
    */
    private $type='html';
    /**
    * $raw 当前页面是否会调用模板输出
    */
    private $raw=false;
    /**
    * 当前页面是否会覆盖后续页面：
    * 如 a/b/c  属于 a/ 的后续
    */
    private $override=false;
    /**
    * 是否满足先决条件
    */
    private $preRule=true;
    /**
     * 网页状态
     * @var null
     */
    private $status=null;
    private $allowOutput=true;
    private $noCache=false;
    private $age=0;
    private $close=false;
    private $cache=null;
    private $filter=null;
    // 渲染控制
    private $noRender=false;
    /**
     * PageController constructor.
     * @param mixed $caller 可调用对象
     * @param array $params
     */
    public function __construct($caller, array $params=[])
    {
        // 设置父类
        parent::__construct($caller, $params);
    }
    public function noCache(bool $nocache=true)
    {
        $this->noCache=$nocache;
    }
    /**
    * 最后参数覆盖
    * 判断是否覆盖后续页面
    * @return bool
    */
    public function useOverride()
    {
        return $this->override;
    }

    /**
     * 设置覆盖属性
     * @param bool $set 设置覆盖属性
     * @return $this
     */
    public function override(bool $set=true)
    {
        $this->override=$set;
        return $this;
    }
    public function allowOutput(bool $set=false)
    {
        $this->allowOutput=$set;
        return $this;
    }
    /**
    * 获取
    */
    public function preg()
    {
        return $this->regs;
    }
    // 前提条件
    public function preRule()
    {
        return $this->preRule;
    }
    // 获取匹配
    public function with($name, $preg)
    {
        if ($preg) {
            if (is_array($name) && is_array($preg)) {
                $arrs=ArrayHelper::combine($name, $preg);
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
    public function id(string $id=null)
    {
        if ($id) {
            $this->id=$id;
            Page::id($id, $this->url);
            return $this; // 链式调用
        }
        return $this->id;
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
    public function use(string $name=null)
    {
        if ($name) {
            $this->raw=false;
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
    public function status(int $status)
    {
        $this->status=$status;
        return $this;
    }
    public function age(int $maxage=0)
    {
        //Cache-control: max-age=5
        if ($maxage) {
            $this->age=$maxage;
            $this->noCache=false;
        }
        return $this;
    }
    public function cache(string $cache)
    {
        $this->cache=$cache;
        return $this;
    }
    public function render(array $value=[])
    {
        if (defined('APP_VERSION')) {
            header('X-Core-App-Version: '.APP_VERSION);
        }
        header('X-Powered-By: DXCore/'.CORE_VERSION);
        if (!is_null($this->status)) {
            send_http_status($this->status);
        }
        // 缓存控制
        if ($this->cache) {
            header('Cache-Control:'.$this->cache);
        } elseif ($this->noCache) {
            header('Cache-Control:no-cache');
        } else {
            header('Cache-control: max-age=' .$this->age);
        }
        if ($this->close) {
            header('Connection:close');
        }
        if ($this->raw) {
            switch ($this->type) {
                case 'json':
                    echo json_encode($value);
                default:
                Page::type($this->type);
                echo Page::getContent();
            }
        } else {
            Page::render($this->tpl, $value);
            if ($this->allowOutput) {
                echo Page::getContent();
            }
        }
    }
    public function isPost()
    {
        $this->preRule=($_SERVER['REQUEST_METHOD'] === 'POST');
        return $this;
    }

    public function accept(string $accept)
    {
        $this->preRule=preg_match('/'.preg_quote($accept).'/i', $_SERVER['HTTP_ACCEPT']);
        return $this;
    }
    public function port($port=80)
    {
        $this->preRule=($_SERVER['SERVER_PORT']===$port);
        return $this;
    }
    // public function userAgent($caller)
    // {
    //     $this->preRule=(new Caller($caller))->call();
    //     return $this;
    // }
    public function isSpider()
    {
        $this->preRule=is_spider();
        return $this;
    }
    public function isGet()
    {
        $this->preRule=($_SERVER['REQUEST_METHOD'] === 'GET');
        return $this;
    }

    public function close()
    {
        $this->close=true;
        return $this;
    }
    public function filter($caller=null)
    {
        if (is_null($caller)) {
            if ($this->filter) {
                return new Caller($this->filter);
            }
            return null;
        }
        $this->filter=$caller;
        return $this;
    }
    public function noRender(bool $noRender=false)
    {
        $this->noRender=$noRender;
        return $this;
    }
    public function renderController()
    {
        return !$this->noRender;
    }
}
