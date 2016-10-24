<?php
namespace Core;

/**
 * 可回调对象
 */
class Caller
{
    public $caller;
    public $file;
    public $static=false;
    public $params=[];
    // TODO : 可以引用文件的调用
    public function __construct($caller, array $params=[])
    {
        $this->caller=$caller;
        $this->params=$params;
    }
    public function params(array $params)
    {
        $this->params=$params;
        return $this;
    }
    public function call(array $params=[])
    {
        if (is_string($this->caller)) {
            $this->caller= self::parseCaller($this->caller);
        }

        // 非空调用
        if ($this->caller) {
            if (count($params)) {
                $this->params=$params;
            }
             // 是函数调用&指定了文件&函数不存在
            if (is_string($this->caller) && !function_exists($this->caller) && $this->file) {
                self::include_file($this->file);
            }
            // 调用接口
            elseif (is_array($this->caller) /*&& !is_callable($this->caller)*/) {
                if (!$this->static) {
                    $this->caller[0]=new $this->caller[0];
                }
            }

            return call_user_func_array($this->caller, $this->params);
        } else {
            // 文件参数引入
            $params=array_unshift($params, $this->file);
            $_SERVER['argv']=$params;
            $_SERVER['args']=count($params);
            if ($this->file) {
                return self::include_file($this->file);
            }
        }
        return false;
    }
    public function args($vargs)
    {
        return self::call(func_get_args());
    }
    protected function parseCaller(string $caller)
    {
        preg_match('/^([\w\\\\]+)?(?:(#|::)(\w+))?(?:@(.+$))?/', $caller, $matchs);
        // 指定文件
        if (isset($matchs[4]) && $matchs[4]) {
            $this->file=$matchs[4];
        }
        if (isset($matchs[2])) {
            $this->static=($matchs[2]!=='#');
        }
        // 方法名
        if (isset($matchs[3]) && $matchs[3]) {
            return [$matchs[1],$matchs[3]];
            // 函数名或类名
        } elseif (isset($matchs[1]) && $matchs[1]) {
            return $matchs[1];
        }
        return null;
    }
    protected static function include_file(string $name)
    {
        static $imported=[];
        if (isset($imported[$name])) {
            return $imported[$name];
        }
        if ($name) {
            $paths=[APP_LIB, CORE_PATH, APP_ROOT]; // 搜索目录
            foreach ($paths as $root) {
                // 优先查找文件
                if (file_exists($require=$root.'/'.$name.'.php')) {
                    $imported[$name]=$require;
                    return require_once $require;
                }
            }
        }
    }
}
