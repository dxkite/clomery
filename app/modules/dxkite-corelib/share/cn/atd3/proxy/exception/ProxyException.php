<?php
namespace cn\atd3\proxy\exception;
use RuntimeException;
class ProxyException extends RuntimeException {
    const NAME_FORMAT=0;
    const EMAIL_FORMAT=1;
    const NAME_EXISTS=3;
    const EMAIL_EXISTS=4;
}