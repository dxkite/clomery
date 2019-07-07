<?php
namespace support\openmethod;

use support\openmethod\MethodParameterBag;

/**
 * 参数构建接口
 */
interface MethodParameterInterface
{
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag);
}
