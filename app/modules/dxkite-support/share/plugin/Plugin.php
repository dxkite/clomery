<?php
namespace dxkite\support\plugin;

/**
 * 插件信息
 */
class Plugin
{
    public $name;
    public $icon;
    public $version;
    public $author;
    public $discription;
    public $modules;
    public $license;
    public $path;
    public $status;
    public $unique;
 
    const ACTIVE = 1;
    const DEACTIVATE = 0;
    const BAD = -1;

    public function __construct(string $path)
    {
        $config = config()->loadConfig($path.'/plugin.json');

        if (!$config) {
            $this->status = Plugin::BAD;
            return;
        }
        
        $this->path= $path;
        $this->name= $config['name'] ?? basename($path);

        if (array_key_exists('icon', $config) && storage()->exist($icon = $path.'/'.$config['icon'])) {
            $this->icon = $icon;
        } else {
            $this->icon = assets(module(__FILE__), 'favicon.ico');
        }

        if (strpos($this->icon, 'http') === false) {
            $iconData=storage()->get($this->icon);
            $mime=mime(pathinfo($this->icon, PATHINFO_EXTENSION));
            $this->icon='data:'.$mime.';base64,'.base64_encode($iconData);
        }

        $this->author=$config['author']??[];

        $this->version=$config['version']??'-';
        $this->discription=$config['discription']??'-';
        
        if (array_key_exists('license', $config) && storage()->exist($license = $path.'/'.$config['license'])) {
            $this->license = $license;
        } else {
            $this->license = $config['license']??'-';
        }
    
        $this->config = array_merge($config, [
            'name' => 'support-plugin/'.$this->name,
            'unique' => 'plugin-'.$this->name,
        ]);
        $this->unique = 'plugin-'.$this->name;
    }
    
    public function register()
    {
        app()->registerModule($this->path, $this->config);
    }

    public function load()
    {
        app()->loadModule($this->config['name']);
    }

    
    public function uninstall()
    {
        $config = app()->getModuleConfig($this->config['name']);
        $installName = self::getModuleFullName($module);
        $installLock = DATA_DIR.'/install/install_'.substr(md5($installName), 0, 6).'.lock';
        if (array_key_exists('uninstall', $config) && !file_exists($installLock)) {
            $uninstalls=$config['uninstall'];
            if (is_string($uninstalls)) {
                $uninstalls=[$uninstalls];
            }
            foreach ($uninstalls as $cmd) {
                cmd($cmd)->args($this);
            }
            storage()->delete($installLock);
        }
    }

    public function active()
    {
        $config = app()->getModuleConfig($this->config['name']);
        if (array_key_exists('active', $config)) {
            $commands=$config['active'];
            if (is_string($commands)) {
                $commands=[$commands];
            }
            foreach ($commands as $cmd) {
                cmd($cmd)->args($this);
            }
        }
    }

    public function deactivate()
    {
        $config = app()->getModuleConfig($this->config['name']);
        if (array_key_exists('deactivate', $config)) {
            $commands=$config['deactivate'];
            if (is_string($commands)) {
                $commands=[$commands];
            }
            foreach ($commands as $cmd) {
                cmd($cmd)->args($this);
            }
        }
    }
}
