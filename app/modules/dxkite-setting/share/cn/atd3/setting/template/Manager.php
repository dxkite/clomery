<?php
namespace cn\atd3\setting\template;

use cn\atd3\ZipHelper;
use suda\template\Manager as tManager;

class Manager
{
    protected $template;
    protected $base;

    protected static $instance;
    const TEMPLATE_DIRNAME='template';

    protected function __construct()
    {
        $this->base=DATA_DIR.'/'.self::TEMPLATE_DIRNAME;
        $this->template=[];
        $this->loadTemplates();
    }

    public static function themeChange(string $uniqid)
    {
        if (isset(self::instance()->template[$uniqid])) {
            $info=self::instance()->template[$uniqid];
            foreach ($info->modules as $module=>$path) {
                tManager::addTemplateSource($module, $path);
            }
        }
    }

    public static function compilerLoad()
    {
        self::themeChange('default');
    }

    public function loadTemplates()
    {
        $dirs=storage()->readDirs($this->base);
        foreach ($dirs as $dir) {
            if (storage()->exist($this->base.'/'.$dir.'/config.json')) {
                $template=new Template($this->base.'/'.$dir.'/config.json');
                $this->template[$template->uniqid]=$template;
            }else{
                storage()->delete($this->base.'/'.$dir);
            }
        }
    }

    public function getTemplateList()
    {
        $list=[];
        foreach ($this->template as $info) {
            if (!is_null($info->icon)) {
                $iconData=storage()->get($info->icon);
                $mime=mime(pathinfo($info->icon, PATHINFO_EXTENSION));
                $info->icon='data:'.$mime.';base64,'.base64_encode($iconData);
            }
            if ($info->license  && storage()->exist($info->license)) {
                $license=storage()->lisc($info->license);
                $info->license=$license;
            }
            $list[]=$info;
        }
        return $list;
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance=new self;
        }
        return self::$instance;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function delete(string $name)
    {
        if (!isset($this->template[$name])) {
            return false;
        }
        return storage()->rmdirs($this->template[$name]->path);
    }

    public function upload(string $path, string $fileName=null)
    {
        $fileName=$fileName??basename($path);
        $name=substr($fileName, 0, strrpos($fileName, '.'));
        return ZipHelper::unzip($path, $this->base.'/'.$name, true);
    }
}
