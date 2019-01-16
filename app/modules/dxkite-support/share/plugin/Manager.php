<?php
namespace dxkite\support\plugin;

use dxkite\support\file\File;
use suda\tool\ZipHelper;

/**
 * 插件管理功能
 */
class Manager
{
    const DIRNAME='plugins';

    protected $plugins = [];
    protected $pluginPath;
    protected $configPath;
    protected $pluginActive;

    protected static $instance;
    
    protected function __construct()
    {
        $this->pluginPath = DATA_DIR.'/'.self::DIRNAME;
        $this->configPath = RUNTIME_DIR.'/plugin.json';
        $this->pluginActive = config()->loadConfig($this->configPath) ?? [];
    }

    public function upload(string $plugin, string $name)
    {
        $name=substr($name, 0, strrpos($name, '.'));
        $path = $this->pluginPath .'/'. $name .'-'.substr(md5_file($plugin), 0, 8);
        if (conf('debug') || ! storage()->isDir($path)) {
            ZipHelper::unzip($plugin, $path, true);
            debug()->info(__('extract plugin $0 to $1', $name, $path));
            return true;
        }
        return false;
    }

    public function loadPlugins()
    {
        foreach (storage()->readDirs($this->pluginPath) as $plugin) {
            $this->loadPlugin($this->pluginPath.'/'.$plugin);
        }
    }

    public function getList():array
    {
        return $this->plugins;
    }

   

    public static function registerPlugins()
    {
        self::instance()->loadPlugins();
    }

    public function loadPlugin(string $pluginPath)
    {
        $plugin = new Plugin($pluginPath);
        if ($plugin->status !== Plugin::BAD) {
            if (in_array($plugin->unique, $this->pluginActive)) {
                $plugin->status = Plugin::ACTIVE;
                $plugin->register();
                $plugin->load();
            }
            $this->plugins[$plugin->unique]=$plugin;
        }
    }

    public function active(string $name)
    {
        if (!in_array($name, $this->pluginActive)) {
            $this->pluginActive[] = $name;
            $this->plugins[$name]->status = Plugin::ACTIVE;
            storage()->put($this->configPath, json_encode($this->pluginActive, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            return true;
        }
        return false;
    }

    public function deactivate(string $name)
    {
        if (($index = array_search($name, $this->pluginActive)) !== false) {
            unset($this->pluginActive[$index]);
            $this->plugins[$name]->status = Plugin::DEACTIVATE;
            storage()->put($this->configPath, json_encode($this->pluginActive, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            return true;
        }
        return false;
    }

    public function delete(string $name)
    {
        if (array_key_exists($name, $this->plugins)) {
            $this->deactivate($name);
            storage()->delete($this->plugins[$name]->path);
            return true;
        }
        return false;
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance=new self;
        }
        return self::$instance;
    }
}
