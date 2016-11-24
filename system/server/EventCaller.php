<?php
namespace server;

class EventCaller
{
    const EVENT_POP=1;
    const EVENT_SHIFT=1<<1;
    const EVENT_ONLY=1<<2;
    const EVENT_BREAK=1<<3;
   

    private $commands=[];
    private $type=self::EVENT_POP;
    private $select=null;

    public function select(string $name)
    {
        $this->select=$name;
        return $this;
    }
    public function args($args)
    {
        self::exec(func_get_args());
    }
    public function add(Command $command)
    {
        $this->commands[]=$command;
        return $this;
    }
    public function exec(array $args=[])
    {
        $break=false;
        // 唯一 弹出栈底对象
        if (self::EVENT_ONLY&$this->type) {
            return array_pop($this->commands)->exec($args);
        }

        if (self::EVENT_BREAK&$this->type) {
            $break=true;
        }
        
        if ($this->type&self::EVENT_SHIFT) {
            while ($callback=array_shift($this->commands)) {
                // // 指定名字
                if (is_string($this->select) && $callback->name() !==$this->select) {
                    continue;
                }
                if ($callback->exec($args)===true && $break) {
                    break;
                }
            }
        } else /* if ($this->type&self::EVENT_POP)*/ {
            while ($callback=array_pop($this->commands)) {
                
                if ( is_string($this->select) && $callback->name() !==$this->select) {
                    continue;
                }
                if ($callback->exec($args)===true && $break) {
                    break;
                }
            }
        }
    }
    public function type(int $type)
    {
        $this->type=$type;
        return $this;
    }
}
