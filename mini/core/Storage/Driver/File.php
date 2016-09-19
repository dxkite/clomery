<?php
    namespace Storage\Driver;

class File implements \Storage\Storage_Driver
{
    // 递归创建文件夹
    public static function mkdirs(string $dir, int $mode=0777)
    {
        if (!self::isDir($dir))
        {
            if (!self::mkdirs(dirname($dir), $mode)) {
                return false;
            }
            if (!@mkdir($dir, $mode)) {
                return false;
            }
        }
        return true;
    }
    // 递归删除文件夹
    public static function rmdirs(string $dir)
    {
        if( $handle=opendir($dir) )
        {
            while(false!== ($item=readdir($handle)) )
            {
                if($item!="."&&$item!="..")
                {
                    if(self::isDir("$dir/$item"))
                    {
                        self::rmdirs("$dir/$item");
                    }
                    else
                    {
                        unlink("$dir/$item");
                    }
                }
            }
        }
    }

    // 创建文件夹
    public static function mkdir(string $dirname, int $mode)
    {
        return mkdir($path,$mode);
    }
    // 删除文件夹
    public static function rmdir(string $dirname)
    {
        return rmdir($path);
    }
    public static function put(string $name, mixed $content)
    {
        return file_put_contents($name,$content);
    }

    public static function get(string $name)
    {
        return file_get_contents($name);
    }

    public static function remove(string $name)
    {
        return unlink($name);
    }
    public static function isFile(string $name)
    {
        return is_file($name);
    }
    public static function isDir(string $name)
    {
        return is_dir($name);
    }
    public static function isReadable(string $name)
    {
        return is_readable($name);
    }
    public static function size(string $name)
    {
        return filesize($name);
    }
    public static function type(string $name)
    {
        return filetype($name);
    }
    public static function parseIniFile(string $file,bool $process_sections = false)
    {
        return parse_ini_file($file,$process_sections);
    }
}