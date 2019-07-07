<?php
namespace support\openmethod\event;

use suda\framework\Config;
use support\openmethod\Permission;
use suda\application\Application;

class WhenLoadConfig
{
    public static function preparePermission(Config $config, Application $application)
    {
        Permission::readPermissions($application);
    }
}
