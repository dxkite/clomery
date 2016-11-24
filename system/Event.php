<?php
use server\Command as Caller;
use server\EventCaller;

class Event
{
    public static $events=[];
    
    public static function listen(string $name, $callback)
    {
        if (!isset(self::$events[$name])) {
            self::$events[$name]=new EventCaller;
        }
        if ($caller instanceof Caller) {
            return self::$insert[$name]->add($caller);
        }
        return self::$events[$name]->add(new Caller($callback));
    }
    public static function pop(string $name, bool $break=false)
    {
        $type=EventCaller::EVENT_POP;
        if ($break) {
            $type|=EventCaller::EVENT_BREAK;
        }
        return isset(self::$events[$name])?self::$events[$name]->type($type):new EventCaller;
    }
    public static function shift(string $name, bool $break=false)
    {
        $type=EventCaller::EVENT_SHIFT;
        if ($break) {
            $type|=EventCaller::EVENT_BREAK;
        }
        return isset(self::$events[$name])?self::$events[$name]->type($type):new EventCaller;
    }
    public static function only(string $name)
    {
        return isset(self::$events[$name])?self::$events[$name]->type(EventCaller::EVENT_ONLY):new EventCaller;
    }

    public static function select(string $name, string $select)
    {
        if (isset(self::$events[$name])) {
            return self::$events[$name]->select($select);
        }
        return false;
    }
}
