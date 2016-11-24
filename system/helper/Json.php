<?php
namespace helper;
class Json
{
    public static $error=[
       JSON_ERROR_NONE=>'No errors',
       JSON_ERROR_DEPTH=>'Maximum stack depth exceeded',
       JSON_ERROR_STATE_MISMATCH=>'Underflow or the modes mismatch',
       JSON_ERROR_CTRL_CHAR=>'Unexpected control character found',
       JSON_ERROR_SYNTAX=>'Syntax error, malformed JSON',
       JSON_ERROR_UTF8=>'Malformed UTF-8 characters, possibly incorrectly encoded',
    ];
    public static function decode()
    {
        return call_user_func_array('json_decode', func_get_args());
    }
    public static function encode()
    {
        return call_user_func_array('json_encode', func_get_args());
    }
    public static function error():string
    {
        return isset($this->error[json_last_error()])?$this->error[json_last_error()]:'Unknown error';
    }
    public static function erron():int
    {
        return json_last_error()===JSON_ERROR_NONE?0:json_last_error();
    }
}
