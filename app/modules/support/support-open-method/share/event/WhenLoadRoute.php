<?php
namespace support\openmethod\event;

use suda\framework\Route;
use suda\framework\Config;
use suda\application\Module;
use suda\application\Application;
use support\openmethod\processor\MethodInterfaceProcessor;

class WhenLoadRoute
{
    public function prepareRoute(Application $application, string $moduleFullName, string $prefix, Module $module, array $routeConfig)
    {
        foreach ($routeConfig as $name => $config) {
            $exname = $application->getRouteName($name, $moduleFullName, 'open-method');
            $method = $config['method'] ?? [];
            $attriute = [];
            $attriute['module'] = $moduleFullName;
            $attriute['open-method'] = $config['class'] ?? [];
            $attriute['group'] = 'open-method';
            $config['class'] = MethodInterfaceProcessor::class;
            $attriute['config'] = $config;
            $attriute['route'] = $exname;
            $attriute['application'] = $application;
            $uri = $config['uri'];
            $uri = '/'.trim($prefix . $uri, '/');
            $application->request($method, $exname, $uri, $attriute);
        }
    }

    public function registerRoute(Route $route, Application $application)
    {
        $application->debug()->info('register open-method routes ...');
        foreach ($application->getModules() as $fullName => $module) {
            if ($path = $module->getResource()->getConfigResourcePath('config/open-method')) {
                $routeConfig = Config::loadConfig($path, [
                    'module' => $fullName,
                    'config' => $module->getConfig(),
                ]);
                if ($routeConfig !== null) {
                    $prefix = $module->getConfig('route-prefix.open-method', '');
                    $this->prepareRoute($application, $fullName, $prefix, $module, $routeConfig);
                }
            }
        }
    }
}
