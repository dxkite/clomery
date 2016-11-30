<?php

use server\Command;
use server\Render;

class Router
{
    protected $request;
    protected $mapper;
    protected $matchs;
    protected $types;
    
    protected $config=SITE_CMD.'/mapper';
    protected $urltype=['int'=>'\d+','string'=>'[^\/]+'];

    protected static $router;

    private function __construct(Request $request)
    {
        $this->request=$request;
        self::loadConfig();
        spl_autoload_register(['Router', 'autoRuningCommand']);
        Event::listen('404_error', 'Router::_error404');
    }

    protected function loadConfig()
    {
        if (file_exists($this->config)) {
            $file=file($this->config);
            foreach ($file as $line) {
                if (preg_match('/^(?:\s*)(?!;)(\w+)\s+(\S+)\s+(\S+)(?:\s+(.+))?$/', $line, $match)) {
                    preg_match('/^([^[]+)(?:\[(?:(\w+)?(?::(\w+))?)\])?/', $match[3], $duri);
                    $mapper=['method'=>$match[1],'url'=>$match[2],'cmd'=>$duri[1]];
                    $value=preg_replace('/\s+/', '&', trim(isset($match[4])?$match[4]:''));
                    parse_str($value, $options);
                    if (isset($duri[2])) {
                        $options['template']=$duri[2];
                    }
                    if (isset($duri[3])) {
                        $options['type']=$duri[3];
                    }
                    $mapper['options']=$options;
                    $id=isset($mapper['options']['id'])?$mapper['options']['id']:count($this->mapper);
                    $this->mapper[$id]=$mapper;
                    $this->matchs[$id]=self::buildMatch($id, $match[2]);
                }
            }
        }
    }

    protected function buildMatch(string $name, string $url)
    {
        $types=&$this->types;
        $urltype=$this->urltype;
        $url=preg_replace('/([\/\.\\\\\+\*\[\^\]\$\(\)\=\!\<\>\|\-])/', '\\\\$1', $url);
        $url=preg_replace_callback('/\{(?:(\w+)(?::(\w+)))\}/', function ($match) use ($name, &$types, $urltype) {
            $size=isset($types[$name])?count($types[$name]):0;
            $param_name=$match[1]!==''?$match[1]:$size;
            $param_type=isset($match[2])?$match[2]:'url';
            $types[$name][$param_name]=$param_type;
            if (isset($urltype[$param_type])) {
                return '('.$urltype[$param_type].')';
            } else {
                return '(.+)';
            }
        }, $url);
        return $url;
    }

    protected function buildUrl(string $name, array $values)
    {
        $url=DIRECTORY_SEPARATOR === '/'?'/':'/?/';
        if (isset($this->mapper[$name])) {
            $url=preg_replace_callback('/\{(?:(\w+)(?::(\w+)))\}/', function ($match) use ($name, $values) {
                $param_name=$match[1];
                $param_type=isset($match[2])?$match[2]:'url';
                if (isset($values[$param_name])) {
                    if ($param_type==='int') {
                        return intval($values[$param_name]);
                    }
                    return $values[$param_name];
                } else {
                    return '';
                }
            }, $this->mapper[$name]['url']);
        } elseif (realpath(SITE_CMD.'/'.$name) ||  realpath(SITE_CMD.'/'.$name.'.php')) {
            $url.=trim($name, '/').'?'.http_build_query($values);
        }
        return $url;
    }
    
    protected function display()
    {
        $return=self::runCommand();
        Page::display($return);
    }
    
    protected function runCommand()
    {
        // mapper
        foreach ($this->matchs as $name=>$preg) {
            if (preg_match('/^'.$preg.'$/', $this->request->url(), $match) && $this->mapper[$name]['method'] === $this->request->method()) {
                array_shift($match);
                foreach ($this->types[$name] as $param_name =>$type) {
                    $value=array_shift($match);
                    if ($type==='int') {
                        $value=intval($value);
                    }
                    $this->request->set($param_name, $value);
                }

                if (isset($this->mapper[$name]['options']['flush']) && $this->mapper[$name]['options']['flush'] == false ) {
                    $render=(new Command($this->mapper[$name]['cmd']))->args($this->request);
                } else {
                    ob_start();
                    $render=(new Command($this->mapper[$name]['cmd']))->args($this->request);
                    $content=ob_get_clean();
                    if ($content) {
                        $this->mapper[$name]['options']['content']=$content;
                    }
                }
                
                Page::setOptions($this->mapper[$name]['options']);
                return $render;
            }
        }

        // auto
        // 路由找不到则使用自动加载
        $rawcmds=[
                    SITE_CMD.'/'.$this->request->url().'.'.strtolower($this->request->method()).'.php', //请求
                    SITE_CMD.'/'.$this->request->url().'.php', // 文件
                    SITE_CMD.'/'.$this->request->url().'/index.php', // 目录
                ];
        
        foreach ($rawcmds as $rawcmd) {
            if (realpath($rawcmd)) {
                $render=require $rawcmd;
                $name=ucfirst(pathinfo($this->request->url(), PATHINFO_FILENAME));
                $namespace=preg_replace('/\//', '\\', trim(dirname($this->request->url()), '/'));
                $class=$namespace!==''?$namespace.'\\'.$name:$name;
                if (preg_match('/^'.preg_quote(SITE_CMD, '/').'/', $rawcmd)) {
                    if (class_exists($class)) {
                        $class=new $class($this->request);

                        if (method_exists($class, 'beforRun')) {
                            $class->beforeRun($this->request);
                        }
                        if (Page::getFlush()) {
                            ob_start();
                            if (method_exists($class, 'afterRun')) {
                                $render=$class->afterRun($class->main($this->request));
                            } else {
                                $render=$class->main($this->request);
                            }
                            $content=ob_get_clean();
                            Page::setContent($content);
                        } else {
                            if (method_exists($class, 'afterRun')) {
                                $render=$class->afterRun($class->main($this->request));
                            } else {
                                $render=$class->main($this->request);
                            }
                        }
                    }
                }
                
                return  $render;
            }
        }
         
        // 啥都找不到
        Event::only('404_error')->args($this->request->url());
    }

    public static function dispatch(Request $request)
    {
        if (!self::$router) {
            self::$router=new Router($request);
        }
        return self::$router->display();
    }

    public static function url(string $id, array $values=null)
    {
        if (!self::$router) {
            self::$router=new Router($request);
        }
        return self::$router->buildUrl($id, $values);
    }

    public function autoRuningCommand($name)
    {
        if ($name) {
            $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $name);
            // 自动加载的情况
            if (file_exists($auto=SITE_CMD.'/'.$fname.'.'.strtolower($this->request->method()).'.php')) {
                return require_once $auto;
            } elseif (file_exists($auto=SITE_CMD.'/'.$fname.'.php')) {
                return require_once $auto;
            } elseif (file_exists($require=SITE_CMD.'/'.$fname.'.cmd.php')) {
                return require_once $require;
            }
        }
    }
    
    public static function _error404($url)
    {
        echo ';-- '.$url.' ;-- 404';
    }
}
