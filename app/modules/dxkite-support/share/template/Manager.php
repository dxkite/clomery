<?php
namespace dxkite\support\template;

use suda\tool\ZipHelper;
use suda\template\Manager as SudaTemplateManager;
use suda\core\Autoloader;
use suda\core\Storage;

/**
 * 模板管理类
 * 管理上传的模板
 */
class Manager
{
    protected $template;
    protected $base;

    protected static $instance;
    const TEMPLATE_DIRNAME='template';
    protected static $current=null;

    protected function __construct()
    {
        $this->base=DATA_DIR.'/'.self::TEMPLATE_DIRNAME;
        $this->template=[];
        $this->loadTemplates();
    }

    public static function changeTheme(string $uniqid)
    {
        if (isset(self::instance()->template[$uniqid])) {
            self::$current=$uniqid;
            $template=self::instance()->template[$uniqid];

            if (is_array($template->modules)) {
                foreach ($template->modules as $module=>$path) {
                    SudaTemplateManager::addTemplateSource($module, $path);
                }
            }
            
            $config = array_merge($template->config, [
                'name' => 'support-template/'.$template->name,
                'unique' => $template->uniqid,
            ]);

            app()->registerModule($template->path, $config);
            app()->loadModule($config['name']);
            app()->addReachableModule($config['name']);
            return true;
        }
        return false;
    }

    public static function hookRouterOverride($router)
    {
        $template = self::instance()->getCurrentTheme();
        if ($template && is_array($template->router)) {
            self::overrideRouter($template->path, $template->router);
        }
    }

    public static function overrideRouter(string $path, array $router)
    {
        foreach ($router as $module => $router) {
            $module = app()->getModuleFullName($module);
            $config = config()->loadConfig($path.'/'.$router, $module);
            self::overrideModuleRouter($module, $config);
        }
    }

    public static function overrideModuleRouter(string $module, array $router)
    {
        foreach ($router as $name => $routerParam) {
            $mapping = router()->getRouter($name, $module);
            if ($mapping) {
                foreach ($routerParam as $name => $value) {
                    if (method_exists($mapping, 'set'.ucfirst($name))) {
                        $mapping->{'set'.ucfirst($name)}($value);
                    }
                }
                router()->refreshMapping($mapping);
            }
        }
    }

    public static function onThemeChanged()
    {
        self::changeTheme(self::getTemplateName());
    }

    public function loadTemplates()
    {
        $dirs=storage()->readDirs($this->base);
        foreach ($dirs as $dir) {
            if (storage()->exist($this->base.'/'.$dir.'/config.json')) {
                $template=new Template($this->base.'/'.$dir.'/config.json');
                $this->template[$template->uniqid]=$template;
            } else {
                storage()->delete($this->base.'/'.$dir);
            }
        }
    }

    public function getTemplateList()
    {
        $list=[];
        foreach ($this->template as $info) {
            if (strpos($info->icon, 'http') === false) {
                $iconData=storage()->get($info->icon);
                $mime=mime(pathinfo($info->icon, PATHINFO_EXTENSION));
                $info->icon='data:'.$mime.';base64,'.base64_encode($iconData);
            }
            
            if ($info->license  && storage()->exist($info->license)) {
                $license=storage()->get($info->license);
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

    public function getCurrentTheme()
    {
        return $this->template[self::$current]??false;
    }

    public function delete(string $name)
    {
        if (\array_key_exists($name, $this->template)) {
            return false;
        }
        return storage()->rmdirs($this->template[$name]->path);
    }

    public function upload(string $path, string $fileName=null)
    {
        $fileName=$fileName??basename($path);
        $name=substr($fileName, 0, strrpos($fileName, '.'));
        return ZipHelper::unzip($path, $this->base.'/'.$name.'-'.substr(md5_file($path), 0, 8), true);
    }

    public static function getTemplateName()
    {
        return setting('template', conf('app.template', 'default'));
    }

    public static function getTemplates()
    {
        $modules=$modules??app()->getLiveModules();
        $templates=[];
        foreach ($modules as $module) {
            if (!app()->checkModuleExist($module)) {
                continue;
            }
            $templates[$module]= SudaTemplateManager::findModuleTemplates($module);
        }
        return $templates;
    }

    public static function exportTemplate(string $themeName, string $output)
    {
        storage()->delete($output);
        $base=$output;
        $theme = self::instance()->template[$themeName];
        storage()->copydir($theme->path, $base);
        $base= \array_key_exists('root', $theme->config)?$base.'/'.$theme->config['root']:$base;
        if (is_array($theme->config['modules'])) {
            foreach ($theme->config['modules'] as $module => $moduleTemplatePath) {
                $outputPath=$base.'/'.$moduleTemplatePath;
                storage()->path($outputPath);
                // 复制模板
                $templates=SudaTemplateManager::findModuleTemplates($module);
                if (is_array($templates)) {
                    foreach ($templates as $name) {
                        if (!storage()->exist($copyTo=$outputPath.'/'.$name.'.tpl.html')) {
                            $templateName=$module.':'.$name;
                            $inputFile=SudaTemplateManager::getInputFile($templateName);
                            if ($inputFile) {
                                storage()->path(dirname($copyTo));
                                storage()->copy($inputFile, $copyTo);
                            }
                        }
                    }
                }
                // 复制静态文件与其他文件
                $sources=SudaTemplateManager::getTemplateSource($module);
                if (is_array($sources)) {
                    foreach ($sources as $source) {
                        if ($path=Storage::abspath($source.'/static')) {
                            storage()->path($outputPath.'/static');
                            storage()->copydir($path, $outputPath.'/static');
                        }
                        storage()->copydir($source, $outputPath, '/(?<!\.tpl\.html)$/');
                    }
                }
            }
        }
    }
}
