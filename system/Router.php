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
                    $mapper['options']=$options;
                    $id=isset($mapper['options']['id'])?$mapper['options']['id']:count($this->mapper);
                    if (isset($duri[2])) {
                        $mapper['template']=$duri[2];
                    }
                    if (isset($duri[3])) {
                        $mapper['type']=$duri[3];
                    }
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
        $url=$this->mapper[$name]['url'];
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
        }, $url);
        return $url;
    }

    protected function display()
    {
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
                $render=(new Command($this->mapper[$name]['cmd']))->args($this->request);
                return (new Render($render))->render($this->mapper[$name]['options']);
            }
        }
    }

    public static function dispatch(Request $request)
    {
        $rawcmd=SITE_CMD.'/'.$request->url().'.auto.php';
        if (realpath($rawcmd)) {
            if (preg_match('/^'.preg_quote(SITE_CMD,'/').'/',$rawcmd)) {
                return require $rawcmd;
            }
        }
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
}
