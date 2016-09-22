<?php
use Core\Caller;

// 简单路由
class Page
{
    private static $maps;
    private static $type=[
        'int'=>'/\d+/',
        'string'=>'/\S+/'
    ];
    private static $names=[];
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
    public static function name(string $name, string $url)
    {
        self::$names[$name]=$url;
    }
    // public static function autourl(string $path)
    // {
    //     $auto=function ($path)
    //     {

    //     };
    //     return self::visit($path.'/{path}',$auto)->with('path','/^(.*)$/');
    // }
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
            if ($success) {
                break;
            }
            // 获取动态参数
            $regs=$caller->preg();
            // 获取动态变量
            preg_match_all('/{(\S+?)}/', $url, $args);
            $url=strlen($url)>1?rtrim($url, '/'):'/';
            // 获取初步匹配的参数
            $regpath=preg_replace(['/\//', '/{(\S+?)}/'], ['\\/', '([^\/]+)'], $url);
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
                    $return=$caller->call($values);
                    if (!is_array($return)) {
                        $return=[$return];
                    }
                    $caller->render($return);
                }
            } elseif (preg_match('/^'.preg_quote($url, '/').'$/', $path)) {
                $success=true;
                $return=$caller->call($values);
                if (!is_array($return)) {
                    $return=[$return];
                }
                $caller->render($return);
            }
        }
        // 默认
        if (!$success && isset(self::$maps['__default__'])) {
            $caller=self::$maps['__default__'];
            $return=$caller->call($values);
            if (!is_array($return)) {
                $return=[$return];
            }
            $caller->render($return);
        }
    }
}
