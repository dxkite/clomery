<?php
namespace cn\atd3\setting\template;

use suda\tool\Json;

class Template
{
    public $uniqid;
    public $name;
    public $icon;
    public $author;
    public $authorLink;
    public $version;
    public $discription;
    public $modules;
    public $license;
    public $path;

    public function __construct(string $path)
    {
        $config=Json::parseFile($path);
        $base=dirname($path);
        $this->path=$base;
        $this->name=$config['name']??basename($base);
        $this->uniqid=basename($base);
        if (storage()->exist($icon=$base.'/'.$config['icon'])) {
            $this->icon=$icon;
        }
        $this->author=$config['author']??'??';
        $this->authorLink=$config['authorLink']??'#';
        $this->version=$config['version']??'-';
        $this->discription=$config['discription']??'-';
        if (storage()->exist($license=$base.'/'.$config['license'])) {
            $this->license = $license;
        } else {
            $this->license=$config['license']??'-';
        }

        foreach (($config['modules']??[]) as $module=>$module_dir) {
            if (is_dir($tpl=$base.'/'.$module_dir)) {
                $this->modules[$module]=$tpl;
            }
        }
    }
}
