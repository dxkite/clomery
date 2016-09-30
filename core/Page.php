<?php
use \Core\PageController  as Page_Controller;
// 简单页面控制
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
        if (isset(self::$ids[$id])) {
            $url=self::$ids[$id];
            foreach ($args as $name =>$value) {
                $url=preg_replace("/\{{$name}\}[?]?/", $value, $url);
            }
            // 去除未设置参数的
            return preg_replace('/\{(\S+?)\}([?])/', '', $url);
        }
        return '/';
    }
    public static function controller()
    {
        return self::$controller;
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
        $auto=function ($path='Main') use ($name_path, $pathroot) {
            if (!$path) {
                $path='Main';
            }
            $names=trim($pathroot.'/'.$path, '/');
            $file=APP_ROOT.'/'.$names.'.php';
            if (Storage::exist($file)) {
                require_once $file;
                $class= preg_replace('/(\\\\+|\/+)/', '\\', $names);
                if (class_exists($class, false)) {
                    $app = new $class();
                    $app ->main();
                }
            } else {
                Page::controller()->status(404)->use(404);
                View::set('title', '页面找不到了哦！');
                View::set('url', $names);
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
                $regpath=preg_replace(['/\//', '/{(\S+?)}([?])?\/?$/', '/{(\S+?)}/'], ['\\/', '(.*)', '([^\/]+)'], $url);
            } else {
                $regpath=preg_replace(['/\//', '/{(\S+?)}/'], ['\\/', '([^\/]+)'], $url);
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
        // 查找资源
        if ($path_raw=View::resource($path)) {
            self::call((new Page_Controller(function ($path_raw) {
                echo Storage::get($path_raw);
            }))->raw()->status(200)->type(pathinfo($path_raw, PATHINFO_EXTENSION)), [$path_raw]);
            $success=true;
        }
        // 默认
        if (!$success && isset(self::$default)) {
            self::call(self::$default, [$path]);
        }
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
        $return=$caller->call($args);
        if (!is_array($return)) {
            $return=[$return];
        }
        $caller->render($return);
    }
}
