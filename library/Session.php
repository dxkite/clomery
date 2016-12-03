<?php


class Session
{
    public static $session;
    /**
     * 设置
     * @param string $name 名
     * @param $value 值
     * @param int $expire 过期时间
     * @return bool
     */
    public static function set(string $name, $value, int $expire=86400):bool
    {
        if ($id =User::getSignInUserId()) {
            $session='session_user_'.$id;
        } else {
            return  false;
        }
        $path=SITE_RESOURCE.'/session/'.$session;
        self::$session[$name]=$value;
        Storage::mkdirs(dirname($path));
        $value=serialize($value);
        return file_put_contents($path, $expire.'|'.$value)!==false;
    }

    /**
     * 获取值
     * @param string $name 名
     * @return mixed|null
     */
    public static function get(string $name, $defalut=null)
    {
        // 有值就获取值
        if (isset(self::$session[$name])) {
            $value=self::$session[$name];
            return $value;
        }
        if ($id =User::getSignInUserId()) {
            $session='session_user_'.$id;
        } else {
            return  $defalut;
        }
       
        
        // 没值就在session文件中查找
        $path=SITE_RESOURCE.'/session/'. $session;
        if (Storage::exist($path)) {
            $value=Storage::get($path);
            $time=explode('|', $value, 2);
            if (time()<intval($time[0]) || intval($time[0])===0) {
                // 未过期则返回
                $value= unserialize($time[1]);
                if (is_array($value)) {
                    return helper\ArrayHelper::get($value, $name, $default);
                }
                return $value;
            } else {
                // 过期则删除
                self::delete($path);
            }
        }
        // 返回默认值
        return $defalut;
    }

    /**
     * 删除值
     * @param string $name 名
     * @return bool
     */
    public static function delete(string $name) :bool
    {
        return Storage::remove(self::nam($name));
    }
    // 检测
    public static function has(string $name):bool
    {
        return self::get($name)!==null;
    }

    /**
     * 垃圾回收
     */
    public static function gc()
    {
        $files=Storage::readDirFiles($path=SITE_RESOURCE.'/session', '/^(?!\.)/');
        foreach ($files as $file) {
            if (conf('NoSession', 0)) {
                Storage::remove($file);
            } else {
                $value=Storage::get($file);
                $time=explode('|', $value, 2);
                if (intval($time[0])!==0 && intval($time[0])<time()) {
                    Storage::remove($file);
                }
            }
        }
    }
}
