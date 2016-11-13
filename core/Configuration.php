<?php
use Core\Arr;

class Configuration
{
    private $conf=null;
    private static $confobj=null;
    
    private function __construct()
    {
        if (file_exists($path=APP_RES.'/'.APP_CONF)  && file_exists(APP_RES.'/install.lock')) {
            $this->conf=parse_ini_file($path, true);
        } elseif (file_exists($path=DOC_ROOT.'/.conf.simple')) {
            $this->conf=parse_ini_file($path, true);
            $this->conf['Uninstall']=!file_exists($path=APP_RES.'/install.lock');
        } else {
            die('<h1>Missing The configure file (DOC_ROOT/'.APP_CONF.'), Please ensure the integrity of the program.</h1> <a href="https://github.com/DXkite/MongCix" title="CLONE ME ON THE GITHUB" >CLONE ME ON THE GITHUB</a>');
        }
    }
    public function getInstance()
    {
        if (is_null(self::$confobj)) {
            self::$confobj =new Configuration();
        }
        return self::$confobj;
    }
    public function reload()
    {
        self::$confobj =new Configuration();
        return self::$confobj;
    }
    
    public function get(string $name, $default=null)
    {
        return Arr::get($this->conf, $name, $default);
    }
}
