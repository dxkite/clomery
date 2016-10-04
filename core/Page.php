<?php
use \Core\PageController  as Page_Controller;
use \Core\Caller;
use \Core\Arr;

/**
 * Class Page
 * 简单页面控制
 */
class Page
{
    private static $maps;
    private static $default;
    private static $type=[
        'int'=>'/\d+/',
        'string'=>'/\S+/'
    ];
    private static $ids=[];
    private static $controller;
    private static $content='';
    private static $values=[];
    private static $use=null;
    private static $insert=[];
    private static $globals=[];
    private static $lang='zh_cn';
    
    public static function insert(string $name, array $args=[])
    {
        // 显示插入点
        if (conf('Debug.showPageHook'))
        {
            echo 'H:['.$name.']';
        }
        if (isset(self::$insert[$name])) {
            foreach (self::$insert[$name] as $caller) {
                $caller->call($args);
            }
        }
    }
    public static function language(string $lang=null)
    {
        return is_null($lang)?self::$lang:self::$lang=$lang;
    }
    public static function insertCallback(string $name, $caller)
    {
        $caller=new Caller($caller);
        self::$insert[$name][]=$caller;
    }
    public static function insertCallbackArray(array $callers)
    {
        foreach ($callers as $name => $caller) {
            self::insertCallback($name, $caller);
        }
    }
    public static function use(string $page)
    {
        self::$use=$page;
    }
    public static function resource($path)
    {
        $extension=pathinfo($path, PATHINFO_EXTENSION);
        // Resource
        if (array_key_exists($extension, mime()) && Storage::exist($path=APP_VIEW.'/'.$path)) {
            return $path;
        }
        return false;
    }
    public static function type(string $type)
    {
        header('Content-type: '.mime($type, 'text/plain;charset=UTF-8'));
    }
    public static function render(string $page, array $values=[])
    {
        // 语言设置
        self::set('lang',self::$lang);
        // 合并数据
        self::assign($values);
        // 内部可设置界面
        $page=is_null(self::$use)?$page:self::$use;
        // 重置页面使用
        self::$use=null;
        // 获取界面路径
        $file=View::viewPath($page);
        // var_dump($file);
        if (Storage::exist($file)) {
            // 类型简化调用
            self::$globals['_Page']=new \Core\Value(self::$values);
            // 多语言支持
            if (!isset(self::$globals['_L'])) {
                self::$globals['_L']=new language(self::$lang);
            }
            // 分解变量
            extract(self::$globals, EXTR_OVERWRITE);
            require_once $file;
        } else {
            trigger_error($page.' TPL no Find!');
        }
    }
    public static function assign(array $values)
    {
        self::$values=array_merge(self::$values, $values);
    }
    public static function global(string $name, $values)
    {
        self::$globals[$name]=$values;
    }
    public static function assignGlobal(array $values)
    {
        self::$globals=array_merge(self::$globals, $values);
    }
    public static function set(string $name, $value)
    {
        self::$values=Arr::set(self::$values, $name, $value);
    }
    public static function default($caller)
    {
        $caller=new Page_Controller($caller);
        self::$default=$caller;
        return $caller;
    }

    /**
     * @param string $id 页面名称
     * @param array $args URL需要的参数
     * @return string 组建的URL
     */
    public static function url(string $id, array $args=[]) : string
    {
        //$host="http://{$_SERVER['SERVER_NAME']}";
        // NOTICE 不记得为什么要加这个了，先去除
        $host='';
        if (isset(self::$ids[$id])) {
            $url=self::$ids[$id];
            foreach ($args as $name =>$value) {
                $url=preg_replace("/\{{$name}\}[?]?/", $value, $url);
            }
            // 去除未设置参数的
            return $host.preg_replace('/\{(\S+?)\}([?])/', '', $url);
        }
        return $host;
    }
    public static function redirect(string $url, int $code=302)
    {
        self::getController()->status($code);
        header('Location:'.$url);
    }
    public static function error404($path=null)
    {
        import('Site.functions');
        Site\page_common_set();
        Page::set('path', $path);
        Page::set('site_title', '页面找不到了哦！');
        Page::getController()->use(404)->status(404);
    }
    public static function id(string $id, string $url)
    {
        self::$ids[$id]=$url;
    }
    // 设置控制器
    public static function visitController(Page_Controller $page)
    {
        self::$maps[$page->url()]=$page;
    }

    /**
     * 自动加载目录下的程序
     * @param string $name_path
     * @param string $pathroot
     * @return $this
     */
    public static function auto(string $name_path, string $pathroot)
    {
        $auto=function ($path='Index') use ($name_path, $pathroot) {
            if (!$path) {
                $path='Index';
            }
            $names=trim($pathroot.'/'.$path, '/');
            $file=APP_ROOT.'/'.$names.'.php';
            if (Storage::exist($file)) {
                require_once $file;
                $class= preg_replace('/(\\\\+|\/+)/', '\\', $names);
                if (class_exists($class, false)) {
                    $app = new $class();
                    return $app ->main();
                }
            } else {
                self::error404(self::url('404_page').'?url='.urlencode($path));
            }
        };
        return self::visit(rtrim($name_path).'/{path}?', $auto)
        ->with('path', '/^(.*)$/')->override();
    }

    public static function visit(string $url, $caller)
    {
        $caller=new Page_Controller($caller);
        $caller->url($url);
        self::$maps[$url]=$caller;
        return $caller;
    }

    public static function display()
    {
        preg_match('/(.*)\/index.php([^?]*)([?].+)?$/', $_SERVER['PHP_SELF'], $match);
        $success=false;
        // 保证URL后面都含有 /
        $path=rtrim($match[2], '/').'/';
        // 开始匹配
        foreach (self::$maps as $url=>$caller) {
            // 满足前提条件
            if (!$caller->preRule()) {
                break;
            }
            // 完成匹配
            if ($success) {
                break;
            }
            // 获取动态参数
            $regs=$caller->preg();
            // 获取动态变量
            preg_match_all('/{(\S+?)}([?])?/', $url, $args);
            
            $url=strlen($url)>1?rtrim($url, '/'):'/';
            // 获取初步匹配的参数
            // 覆盖后续
            if ($caller->useOverride()) {
                $regpath=preg_replace(['/([\/\.\\\\\+\*\[\^\]\$\(\)\=\!\<\>\|\:\-])/', '/{(\S+?)}([?])?\/?$/', '/{(\S+?)}/'], ['\\\\$1', '(.+)$2', '([^\/]+)'], $url);
            } else {
                $regpath=preg_replace(['/([\/\.\\\\\+\*\[\^\]\$\(\)\=\!\<\>\|\:\-])/', '/{(\S+?)}/'], ['\\\\$1', '([^\/]+)'], $url);
            }
            // 检查是否有要匹配的动态变量
            // 检查变量是否存在URL中
            if (count($regs)===count($args[1]) && preg_match('/^'.$regpath.'\/?$/', $path, $values)) {
                // 初步验证成功
                $success=true;
                // 去除第一个值
                array_shift($values);
                // 去除非必须参数
                if (count($args[1])>count($values)) {
                    while (end($args[2])==='?') {
                        array_pop($args[2]);
                        array_pop($args[1]);
                    }
                }
                $keymap=array_combine($args[1], $values);
                foreach ($regs as $name => $preg) {
                    // 载入内置类型
                    if (array_key_exists($preg, self::$type)) {
                        $preg=self::$type[$preg];
                    }
                    // 类型再次验证
                    if (isset($keymap[$name]) && !preg_match($preg, $keymap[$name])) {
                        $success=false;
                    }
                }

                if ($success) {
                    self::call($caller, $values);
                }
                
            } elseif (preg_match('/^'.preg_quote($url, '/').'$/', $path)) {
                $success=true;
                self::call($caller, [$path]);
            }
        }
        // 默认
        if (!$success && isset(self::$default)) {
            self::call(self::$default, [$path]);
        }
    }

    /**
     * @return mixed
     */
    public static function getController()
    {
        return self::$controller;
    }

    /**
     * @return string
     */
    public static function getContent(): string
    {
        return self::$content;
    }

    /**
     * 调用控制器，渲染页面
     * @param Page_Controller $caller 可回调对象
     * @param array $args 调用参数
     */
    private function call(Page_Controller $caller, array $args)
    {
        // 将控制器压入当前控制器
        self::$controller=$caller;
        ob_start();
        $return=$caller->call($args);
        self::$content=ob_get_clean();
        if (!is_array($return)) {
            $return=[$return];
        }
        $caller->render($return);
    }
}
