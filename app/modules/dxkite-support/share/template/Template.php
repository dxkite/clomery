<?php
namespace dxkite\support\template;


/**
 * 模板信息文件
 */
class Template
{
    public $uniqid;
    public $name;
    public $icon;

    public $version;
    public $discription;
    public $modules;
    public $license;
    public $path;
    public $config;
    public $import;
    public $router;
    public $value;
    public $valueNamespace;
    public $require;
    public $data;

    public function __construct(string $path)
    {
        $config=config()->loadConfig($path);

        $base=dirname($path);
        
        $this->path=$base;
        $this->name=$config['name']??basename($base);
        $this->uniqid=basename($base);
        
        if (array_key_exists('icon', $config) && storage()->exist($icon= $base.'/'.$config['icon'])) {
            $this->icon = $icon;
        } else {
            $this->icon = assets(module(__FILE__), 'favicon.ico');
        }

        $this->author=$config['author']??[];

        $this->version=$config['version']??'-';
        $this->discription=$config['discription']??'-';
        
        if (array_key_exists('license', $config) && storage()->exist($license=$base.'/'.$config['license'])) {
            $this->license = $license;
        } else {
            $this->license = $config['license']??'-';
        }

        $base=isset($config['root'])?$base.'/'.$config['root']:$base;
        if (array_key_exists('modules',$config) && is_array($config['modules'])) {
            foreach ($config['modules'] as $module=>$module_dir) {
                if (storage()->exist($base.'/'.$module_dir)) {
                    $this->modules[$module]= $base.'/'.$module_dir;
                }
            }
        }
    
        $this->config=$config;
        $this->import=$config['import']??[];
        $this->router=$config['router']??null;
        $this->value=$config['value']??[];
        $this->valueNamespace= $config['valueNamespace']??'value';
        $this->require=$config['require']??[];
        $this->data=$config['data']??'data';
    }
}
