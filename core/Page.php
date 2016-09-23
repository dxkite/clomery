<?php

// 简单页面控制
class Page
{
    private static $maps;
    private static $type=[
        'int'=>'/\d+/',
        'string'=>'/\S+/'
    ];
    private static $names=[];
    private static $controller;
    public static function default($caller)
    {
        $caller=new Page_Controller($caller);
        self::$maps['__default__']=$caller;
        return $caller;
    }
    public static function url(string $name, array $args)
    {
        if (isset(self::$names[$name])) {
            $url=self::$names[$name];
            foreach ($args as $name =>$value) {
                $url=preg_replace("/\{{$name}\}/", $value, $url);
            }
            return $url;
        }
        return '/';
    }
    public static function controller()
    {
        return self::$controller;
    }
    public static function name(string $name, string $url)
    {
        self::$names[$name]=$url;
    }
    // 设置控制器
    public static function visitController(Page_Controller $page)
    {
        self::$maps[$page->url()]=$page;
    }
    // 自动加载App目录下的程序
    public static function auto(string $name_path, array $search_path)
    {
        $auto=function ($path) use ($search_path) {
            foreach ($search_path as $pathroot) {
                $names=trim($pathroot.'/'.$path, '/');
                $file=APP_ROOT.'/'.$names.'.php';
                if (Storage::exist($file)) {
                    require_once $file;
                    $class= preg_replace('/(\\\\+|\/+)/', '\\', $names);
                    if (class_exists($class, false)) {
                        $app = new $class();
                        $app ->main();
                    }
                }
            }
        };
        return self::visit(rtrim($name_path).'/{path}', $auto)
        ->with('path', 'string')->override();
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
        $path=$match[2]?rtrim($match[2], '/'):'/';
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
            preg_match_all('/{(\S+?)}/', $url, $args);
            $url=strlen($url)>1?rtrim($url, '/'):'/';
            // 获取初步匹配的参数
            // 覆盖后续
            if ($caller->useOverride()) {
                $regpath=preg_replace(['/\//', '/{(\S+?)}\/?$/', '/{(\S+?)}/'], ['\\/', '(.*)', '([^\/]+)'], $url);
            } else {
                $regpath=preg_replace(['/\//', '/{(\S+?)}/'], ['\\/', '([^\/]+)'], $url);
            }
            // 检查是否有要匹配的动态变量
            // 检查变量是否存在URL中
            if (count($regs)===count($args[1]) && preg_match('/^'.$regpath.'$/', $path, $values)) {
                // 初步验证成功
                $success=true;
                // 去除第一个值
                array_shift($values);
                $keymap=array_combine($args[1], $values);
                foreach ($regs as $name => $preg) {
                    if (array_key_exists($preg, self::$type)) {
                        $preg=self::$type[$preg];
                    }
                     
                    if (!preg_match($preg, $keymap[$name])) {
                        $success=false;
                    }
                }
                if ($success) {
                    self::call($caller, $values);
                }
            } elseif (preg_match('/^'.preg_quote($url, '/').'$/', $path)) {
                $success=true;
                self::call($caller, $values);
            }
        }
        // 查找资源
        if (View::resource($path)) {
            $success=true;
        }
        // 默认
        if (!$success && isset(self::$maps['__default__'])) {
            self::call(self::$maps['__default__'], [$path]);
        }
    }
    
    private function call(Page_Controller $caller, array $args)
    {
        self::$controller=$caller;
        $return=$caller->call($args);
        if (!is_array($return)) {
            $return=[$return];
        }
        $caller->render($return);
    }
}
