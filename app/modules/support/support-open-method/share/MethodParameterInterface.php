<?php
namespace support\openmethod;

use suda\framework\Request;
use suda\application\Application;

/**
 * 参数构建接口
 */
interface MethodParameterInterface
{
    public static function createParameterFromRequest(int $position, string $name, string $from, Application $application, Request $request, $json);
}
