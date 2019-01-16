<?php
namespace dxkite\user;

use dxkite\support\visitor\VerifyCode;

class HumanCode
{
    const HUMANCODE='HUMANCODE';
    
    public static function check(string $code)
    {
        return (new VerifyCode(HumanCode::HUMANCODE))->checkCode($code);
    }

    public static function display()
    {
        return (new VerifyCode(HumanCode::HUMANCODE))->display();
    }

    public static function hasCode()
    {
        return (new VerifyCode(HumanCode::HUMANCODE))->hasCode();
    }
}
