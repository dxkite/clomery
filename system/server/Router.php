<?php
namespace server;

class Router
{
    protected $request;
    protected $mapper;
    protected $matchs;
    protected $types;

    protected $config=SITE_CMD.'/mapper';
    protected $urltype=['int'=>'\d+','string'=>'[^\/]+'];

    public function __construct(\Request $request)
    {
        $this->request=$request;
        self::loadConfig();
    }

    public function loadConfig()
    {
        if (file_exists($this->config)) {
            $file=file($this->config);
            foreach ($file as $line) {
                if (preg_match('/^(?:\s*)(?!;)(\w+)\s+(\S+)\s+(\S+)(?:\s+(\S+))?\s*?$/', $line, $match)) {
                    $name=isset($match[4])?$match[4]:count($this->mapper);
                    preg_match('/^([^[]+)(?:\[(?:(\w+)?(?::(\w+))?)\])?/', $match[3], $duri);
                    $mapper=['method'=>$match[1],'url'=>$match[2],'cmd'=>$duri[1]];
                    if (isset($duri[2])) {
                        $mapper['template']=$duri[2];
                    }
                    if (isset($duri[3])) {
                        $mapper['type']=$duri[3];
                    }
                    $this->mapper[$name]=$mapper;
                    $this->matchs[$name]=self::buildMatch($name, $match[2]);
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

    public function dispatch()
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
                return (new Render($render))->render();
            }
        }
    }
}
