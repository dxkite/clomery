<?php
namespace support\openmethod\exception;

use RuntimeException;
use support\openmethod\Permission;

/**
 * 权限异常
 */
class PermissionException extends RuntimeException
{

    /**
     * 权限
     *
     * @param Permission|string|array $permission
     */
    public function __construct($permission)
    {
        if ($permission instanceof Permission) {
            $permission = $permission->toArray();
        } elseif (is_string($permission)) {
            $permission = [$permission];
        }
        parent::__construct('required '. \implode(',', $permission));
    }
}
