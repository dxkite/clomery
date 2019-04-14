<?php
namespace support\setting\event;

use suda\framework\Config;
use suda\application\Application;

class GlobalObject
{
    /**
     * 全局引用
     *
     * @var Application
     */
    public static $application;

    public static function loadApplication(Config $config, Application $application)
    {
        static::$application = $application;
    }
}
