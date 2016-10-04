<?php
namespace Core;

class EventCaller
{
    const EVENT_POP=1;
    const EVENT_SHIFT=1<<1;
    const EVENT_BREAK=1<<2;

    public $callers=[];
    public $type=self::EVENT_POP;

    public function args($args)
    {
        self::call(func_get_args());
    }
    public function add(Caller $caller)
    {
        $this->callers[]=$caller;
        return $this;
    }
    public function call(array $args=[])
    {
        $break=false;
        if (self::EVENT_BREAK&$this->type) {
            $break=true;
        }
        if ($this->type&self::EVENT_SHIFT) {
            while ($callback=array_shift($this->callers)) {
                if ($break &&  $callback->call($args)===true) {
                    break;
                }
            }
        } else /* if ($this->type&self::EVENT_POP)*/ {
            while ($callback=array_pop($this->callers)) {
                if ($break &&  $callback->call($args)===true) {
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
