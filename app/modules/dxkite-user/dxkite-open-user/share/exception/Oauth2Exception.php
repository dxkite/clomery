<?php
namespace dxkite\openuser\exception;

class Oauth2Exception extends \Exception
{
    const ERR_SYSTEM = 50000; // 系统异常
    const ERR_APPID = 50001; // APPID不可用
    const ERR_CODE = 50002; // 错误的临时令牌
    const ERR_ACCESS_TOKEN = 50003; // 错误的访问令牌
    const ERR_REFRESH_TOKEN = 50004; // 错误的更新令牌
    const ERR_USER = 50005; // 错误的用户
    const ERR_HOSTNAME = 50006; // 错误的域名
}
