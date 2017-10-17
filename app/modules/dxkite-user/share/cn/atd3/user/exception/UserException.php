<?php
namespace cn\atd3\exception;
use RuntimeException;
class UserException extends RuntimeException {
    const NAME_FORMAT=0;
    const EMAIL_FORMAT=1;
    const NAME_EXISTS=3;
    const EMAIL_EXISTS=4;
}