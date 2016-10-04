<?php
use Core\Caller;
use Core\EventCaller;

class Event
{
    public static $events=[];
    public static function addPageListener(string $name, $callback)
    {
        Page::insertCallback($name, $callback);
    }
    public static function addEventListener(string $name, $callback)
    {
        if (!isset(self::$events[$name])) {
            self::$events[$name]=new EventCaller;
        }
        self::$events[$name]->add(new Caller($callback));
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
}
