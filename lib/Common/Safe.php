<?php

class Common_Safe
{
    // 暂时不管，输出可以避免XSS
    public static function parseXSS(string $string, int $maxlen=128)
    {
        return preg_match('/\S+/', '-', $string);
    }
}
