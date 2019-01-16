<?php
namespace dxkite\user\sms;

class SMSFactory
{
    public static function sender() :?Sender
    {
        $sms = conf('sms.type', 'tencent');
        $class = conf('sms.'.$sms.'.sender');
        if ($class && $class::isAvailable()) {
            return new $class;
        } else {
            return null;
        }
    }

    public static function isAvailable():bool {
        return self::sender() !== null;
    }
}
