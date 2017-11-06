<?php
namespace cn\atd3\api;

use suda\core\route\Mapping;

class Hook
{
    public static function setting($template)
    {
        $template->addcommand('api', function ($exp) {
            if (preg_match('/\((.+)\)/', $exp, $v)) {
                $name=trim($v[1], '"\'');
                if(preg_match('/^:(.+)$/',$name,$match)){
                    $prefix=app()->getModulePrefix('dxkite/corelib')['api']??'/open-api';
                    return  request()->hostBase().$prefix.'/'.$match[1]??'';
                }
                if(strpos($name,':')){
                   list($name,$version)=preg_split('/:/',$name,2);
                }
                return u('dxkite/corelib:api_'.$name.'_'.$version);
            } else {
                $prefix=app()->getModulePrefix('dxkite/corelib')['api']??'/open-api';
                return  request()->hostBase().$prefix;
            }
        });
    }

    public static function registerOpenApiRoute($router)
    {
        $modules = app()->getLiveModules();
        $prefix=app()->getModulePrefix('dxkite/corelib')['api']??'/open-api';
        foreach ($modules as $module) {
            $config=app()->getModuleConfig($module);
            if (isset($config['api-proxy'])) {
                foreach ($config['api-proxy'] as $version => $classFields) {
                    foreach ($classFields as $name => $proxyClass) {
                        $mapping=new Mapping('api_'.$name.'_'.$version, $prefix.'/'.$version.'/'.$name.'[/{method}]', Response::class.'->onRequest', 'dxkite/corelib');
                        $mapping->setAntiPrefix();
                        $mapping->setParam([
                            'proxyClass'=>$proxyClass,
                        ]);
                        $router->addMapping($mapping);
                    }
                }
            }
        }
    }
}
