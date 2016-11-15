<?php
use Core\ArrayHelper;

class Configuration
{
    private $conf=null;
    private static $instance=null;
    
    private function __construct()
    {
        if (file_exists($path=APP_RES.'/'.APP_CONF)  && file_exists(APP_RES.'/install.lock')) {
            $this->conf=parse_ini_file($path, true);
        } elseif (file_exists($path=DOC_ROOT.'/.conf.simple')) {
            $this->conf=parse_ini_file($path, true);
        } else {
            die('<h1>Missing The configure file (DOC_ROOT/'.APP_CONF.'), Please ensure the integrity of the program.</h1> <a href="https://github.com/DXkite/MongCix" title="CLONE ME ON THE GITHUB" >CLONE ME ON THE GITHUB</a>');
        }
        $this->conf['Uninstall']=!file_exists(APP_RES.'/install.lock');
    }

    public function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance =new self();
        }
        return self::$instance;
    }
    public function reload()
    {
        self::$instance =new self();
        return self::$instance;
    }

    public function get(string $name, $default=null)
    {
        return ArrayHelper::get($this->conf, $name, $default);
    }
}
