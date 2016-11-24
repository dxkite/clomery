<?php
namespace server;

class Command
{
    public $command;
    public $file;
    public $static=false;
    public $params=[];
    public $func_bind=[];
    public $name;

    public function __construct($command, array $params=[])
    {
        $this->command=$command;
        $this->params=$params;
    }
    public function name(string $name='')
    {
        if ($name) {
            $this->name=$name;
        }
        return $this->name;
    }
    public function params(array $params)
    {
        $this->params=$params;
        return $this;
    }

    public function exec(array $params=[])
    {
        if (is_string($this->command)) {
            $this->command= self::parseCommand($this->command);
        }
        // 集合所有参数
        if (count($params)) {
            $this->params=$params;
        }

        // 设置了参数绑定
        if (count($this->func_bind)>0) {
            $args=[];
            foreach ($this->func_bind as $index=>$bind) {
                $args[$index]=isset($this->params[$bind])?$this->params[$bind]:null;
            }
            $this->params=$args;
        }
        spl_autoload_register([$this,'loadCommand']);
        // 非空调用
        if ($this->command) {
            // 是函数调用&指定了文件&函数不存在
            if (is_string($this->command) && !function_exists($this->command) && $this->file) {
                self::loadCommand($this->file);
            }
            // 调用接口
            elseif (is_array($this->command) /*&& !is_callable($this->command)*/) {
                if ($this->static) {
                } else {
                    $this->command[0]=new $this->command[0];
                }
            }
            return call_user_func_array($this->command, $this->params);
        } elseif ($this->file) {
            // 文件参数引入
            $params=array_unshift($params, $this->file);
            $_SERVER['argv']=$params;
            $_SERVER['args']=count($params);
            return self::loadCommand($this->file);
        }
        return false;
    }
    public function args($vargs)
    {
        return self::exec(func_get_args());
    }
    protected function parseCommand(string $command)
    {
        preg_match('/^(?:([\w\\\\]+))?(?:(#|->|::)(\w+))?(?:\((.+?)\))?(?:@(.+$))?/', $command, $matchs);
        // 添加参数绑定
        if (isset($matchs[4])) {
            $this->func_bind=explode(',', trim($matchs[4], ','));
        }
        // 指定文件
        if (isset($matchs[5]) && $matchs[5]) {
            $this->file=$matchs[5];
        }
        // 调用方式
        if (isset($matchs[2])) {
            $this->static=(strcmp($matchs[2], '::')===0);
        }
        // 方法名
        if (isset($matchs[3]) && $matchs[3]) {
            return [$matchs[1],$matchs[3]];
            // 函数名
        } elseif (isset($matchs[1]) && $matchs[1]) {
            return $matchs[1];
        }
        return null;
    }
    protected static function loadCommand(string $name)
    {
        static $imported=[];
        if (isset($imported[$name])) {
            return $imported[$name];
        }
        if ($name) {
            $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $name);
            $paths=[__DIR__];
            $command=[SITE_CMD];
            // 普通
            foreach ($paths as $root) {
                if (file_exists($require=$root.'/'.$fname.'.php')) {
                    $imported[$name]=$require;
                    return require_once $require;
                }
            }
            // 运行时命令
            foreach ($command as $root) {
                if (file_exists($require=$root.'/'.$fname.'.cmd.php')) {
                    $imported[$name]=$require;
                    return require_once $require;
                }
            }
            if (file_exists($name)) {
                return require_once $name;
            }
        }
    }
}
