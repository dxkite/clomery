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
     * @param int $code
     */
    public function __construct($permission, int $code = 0)
    {
        $show = [];
        if ($permission instanceof Permission) {
            $permission = $permission->jsonSerialize();
            foreach ($permission as $key => $item) {
                $show[] = $key.'('.$item.')';
            }
        } elseif (is_array($permission)) {
            $show = $permission;
        } elseif (is_string($permission)) {
            $show = [$permission];
        }
        parent::__construct('required permission: '. \implode(',', $show), $code);
    }
}
