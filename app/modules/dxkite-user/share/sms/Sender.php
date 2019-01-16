<?php
namespace dxkite\user\sms;

interface Sender
{
    public function send(string $mobile, string $action, string $code):?bool;
    public static function isAvailable():?bool;
}
