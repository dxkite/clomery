<?php
namespace dxkite\article;

use suda\tool\Security;
use suda\template\Manager;

trait TemplateCSPLoader 
{   
    protected function getContentSecurityPolicy() {

        list($root,$path) = Manager::getInputFile(app()->getActiveModule(),'csp-white-list.json',false);
        $config =  Security::getDefaultCsp();
        if (!is_null($path)) {
            $cspRules = config()->loadConfig($path);
            if (\is_array($cspRules)) {
                foreach($cspRules as $name => $value) {
                    if (\array_key_exists($name.'-src',$config)) {
                        $config[$name.'-src'] = \array_merge( $config[$name.'-src'], $value);
                    }else{
                        $config[$name.'-src'] = $value;
                    }
                }
            }
        }
        return $config;
    }
}
