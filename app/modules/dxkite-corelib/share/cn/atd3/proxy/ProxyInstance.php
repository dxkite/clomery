<?php
namespace cn\atd3\proxy;

use cn\atd3\proxy\exception\ProxyException;
use cn\atd3\visitor\Context;

class ProxyInstance
{
    protected static $tableInstances;
    protected static $instance;
    protected static $proxyClass;

    protected function __construct()
    {
        $this->getProxyNames();
    }

    public static function new(string $proxyName)
    {
        $proxyClassName=self::instance()->getClassName($proxyName);
        return self::$tableInstances[$proxyName]=new $proxyClassName(Context::getInstance());
    }

    public static function getInstance(string $proxyName)
    {
        if (isset(self::$tableInstances[$proxyName])) {
            return self::$tableInstances[$proxyName];
        }
        return self::new($proxyName);
    }

    protected static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance=new self;
        }
        return self::$instance;
    }

    protected function getProxyNames()
    {
        $modules = app()->getLiveModules();
        foreach ($modules as $module) {
            $this->getProxyNameFromModule($module);
        }
    }

    protected function getProxyNameFromModule(string $module)
    {
        $config=app()->getModuleConfig($module);
        if (isset($config['proxy'])) {
            foreach ($config['proxy'] as $name=>$class) {
                $className=class_name($class);
                if (is_string($name)) {
                    $proxyName=$name;
                } else {
                    $proxyName=substr(strrchr($className, '\\'), 1);
                }
                self::$proxyClass[$proxyName]=$className;
            }
        }
    }
    
    protected function formatProxyName(string $className)
    {
        return $className;
    }

    protected function getClassName(string $proxyName)
    {
        $tableRawName=$this->formatProxyName($proxyName);
        if (isset(self::$proxyClass[$tableRawName])) {
            return self::$proxyClass[$tableRawName];
        } else {
            throw new ProxyException(__('proxy %s class not exist', $proxyName));
        }
    }
}
