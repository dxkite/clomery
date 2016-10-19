<?php
// 安装任务
/*
* 任务格式 class#method@filename
*/
class Install_Tasks
{
    static $task=[];
    static $taskfile=__DIR__.'/install.task';
    public static function loadTask(){
        if (Storage::exsit(self::$taskfile)){
            self::$task=unserialize(Storage::get(self::$taskfile));
        }
    }

    public static function addTask(string $name,string $do, string $info='',string $edo='')
    {
        self::$task[$name]=['do'=>$do,'info'=>$info,'errdo'=>$edo];
    }
    
    public static function parseDo(string $do){
        preg_match('/^(\w+)?(?:#(\w+))?(?:@(.+$))?/',$do,$matchs);
        return ['class'=>isset($matchs[1])?$matchs[1]:'','method'=>isset($matchs[2])?$matchs[2]:'main','file'=>isset($matchs[3])?$matchs[3]:''];
    }

    public static function doTask(string $name) 
    {
        if (isset(self::$task[$name])){
            $return=false;
            $task=self::parseDo(self::$task[$name]['do']);
            if ($task['file'] && Storage::exist(APP_LIB.'/'.$task['file'])){
                $return = require APP_LIB.'/'.$task['file'];
            }
            if ($task['class']){
                return (new $task['class'])->{$task['method']}();
            }
        }
        return false;
    }

    public static function saveTask(){
        return Storage::put(self::$taskfile,serialize(self::$task));
    }
}
