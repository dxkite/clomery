<?php
namespace dxkite\support\api;

use suda\core\route\Mapping;

class Hook
{
    public static function setting($template)
    {
        /**
         * xxx:version
         */
        $template->addcommand('api', function ($exp) {
            $module = module(__FILE__);
            if (preg_match('/\((.+)\)/', $exp, $v)) {
                $name=trim($v[1], '"\'');
                return '<?php echo u("'.$module.':".'.__CLASS__.'::name2router("'.$name.'")); ?>';
            } else {
                return '<?php echo u("'.$module.':".'.__CLASS__.'::name2router()); ?>';
            }
        });
    }

    /**
     * 注册标准开放URL
     *
     * @param [type] $router
     * @return void
     */
    public static function registerOpenApiRoute($router)
    {
        $modules = app()->getReachableModules();
        $prefix=app()->getModulePrefix(module(__FILE__))['api']??'/api';
        $apiMenu=[];
        $apiVersionMenu=[];
        foreach ($modules as $module) {
            $mapper = app()->getModuleConfig($module, 'api/mapper');
            if (is_array($mapper)) {
                foreach ($mapper as $version => $classFields) {
                    foreach ($classFields as $name => $proxyClass) {
                        $mapping=new Mapping(self::buildRouterName($version, $name), $prefix.'/'.$version.'/'.$name.'[/{method}]', Response::class.'->onRequest', module(__FILE__), [], 'support_api');
                        $mapping->setAntiPrefix();
                        $mapping->setParam([
                            'proxyClass'=>$proxyClass,
                            'module' => $module,
                        ]);
                        $router->addMapping($mapping);
                        $apiMenu[$version][$name]=$mapping;
                    }
                }
            }
        }

        foreach ($apiMenu as $version => $apiSet) {
            $mapping=new Mapping(self::buildRouterName($version), $prefix.'/'.$version, MenuResponse::class.'->onRequest', module(__FILE__), [], 'support_api');
            $mapping->setAntiPrefix();
            $mapping->setParam(['mappingMenu'=>$apiSet]);
            $router->addMapping($mapping);
            $apiVersionMenu[$version]=$mapping;
        }

        $mapping=new Mapping(self::buildRouterName(), $prefix, MenuResponse::class.'->onRequest', module(__FILE__), ['GET'], 'support_api');
        $mapping->setAntiPrefix();
        $mapping->setParam(['mappingMenu'=>$apiVersionMenu]);
        $router->addMapping($mapping);
    }

    /**
     * 将名字解析到对应的API路由
     * name:version
     * @param string $name
     * @return string
     */
    public static function name2router(string $name=''):string
    {
        // :version
        if (strpos($name, ':') === 0) {
            return self::buildRouterName(substr($name, 1));
        }
        // name:version
        if (strpos($name, ':') > 0) {
            list($name, $version) = explode(':', $name, 2);
            return self::buildRouterName($version, $name);
        }
        return self::buildRouterName();
    }

    protected static function buildRouterName(?string $version = null, ?string $name = null)
    {
        if (is_null($version) && is_null($name)) {
            return 'rpc_api';
        } elseif (is_null($name)) {
            return 'rpc_api_'.$version;
        } else {
            return 'rpc_api_'.$version.'_'.$name;
        }
    }
}
