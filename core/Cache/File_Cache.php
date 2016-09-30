<?php
/**
* 文件缓存
*/
class Cache implements Cache_Interface
{
    public static $cache;
    // 设置
    public static function set(string $name, $value, int $expire=0)
    {
        $path=APP_RES.'/cache/'.self::nam($name);
        self::$cache[$name]=$value;
        Storage::mkdirs(dirname($path));
        $value=serialize($value);
        return file_put_contents($path, $expire.'|'.$value);
    }
    // 获取
    public static function get(string $name)
    {
        if (isset(self::$cache[$name])) {
            $value=self::$cache[$name];
            return is_array($value)?Core\Arr::get($value, $name):$value;
        }
        $path=APP_RES.'/cache/'.self::nam($name);
        if (Storage::exist($path))
        {
            $value=Storage::get($path);
            $time=explode('|', $value, 2);
            if (time()<intval($time[0])) {
                $value=unserialize($time[1]);
                return is_array($value)?Core\Arr::get($value, $name):$value;
            }
        }
        return null;
    }
    // 删除
    public static function delete(string $name)
    {
        return Storage::remove(self::nam($name));
    }
    // 检测
    public static function has(string $name):bool
    {
        return self::get($name)!==null;
    }
    // 替换
    public static function replace(string $name, $value, int $expire=0)
    {
        $get_value=self::get($name);
        if (is_array($get_value)) {
            $get_value=Core\Arr::set($get_value, $name, $value);
        } else {
            $get_value=$value;
        }
        return self::set($name, $get_value, $expire);
    }
    // 垃圾回收
    public static function gc()
    {
        $files=Storage::readDirFiles($path=APP_RES.'/cache','/^(?!\.)/');
        foreach ($files as $file) {
            $value=Storage::get($file);
            $time=explode('|', $value, 2);
            if (intval($time[0])<time()) {
                Storage::remove($file);
            }
        }
    }
    private static function nam(string $name)
    {
        $str=preg_split('/[.\/]+/', $name, 2,PREG_SPLIT_NO_EMPTY);
        return $str[0].'_'.md5($name);
    }
}
