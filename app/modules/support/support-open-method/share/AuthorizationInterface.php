<?php
namespace support\openmethod;

use support\openmethod\Permission;

/**
 * 授权访问控制接口
 */
interface AuthorizationInterface
{
    public function getPermission():Permission;
}
