<?php
namespace dxkite\support\visitor\response;

interface MethodParameter
{
    public static function createFromJson($jsonData):?object;
    public static function createFromPost(string $name,$postData):?object;
}
