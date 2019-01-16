<?php
namespace dxkite\user\exception;

use RuntimeException;

class UserException extends RuntimeException
{
    const NAME_FORMAT_ERROR=-1;
    const EMAIL_FORMAT_ERROR=-2;
    const NAME_EXISTS_ERROR=-3;
    const EMAIL_EXISTS_ERROR=-4;
    const ACCOUNT_OR_PASSWORD_ERROR=-5;
    const USER_FREEZED=-6;
    const HUMAN_CODE_ERROR=-7;
    const INVITE_CODE_ERROR=-8;
    const ACCOUNT_IS_NOT_ACTIVE=-9;
    const MOBILE_FORMAT_ERROR = -10;
    const MOBILE_EXISTS_ERROR = -11;
}
