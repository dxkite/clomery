<?php

use support\setting\event\GlobalObject;

/**
 * 语言引用
 */
function __(string $message, ...$_) {
    return GlobalObject::$application->_($message, ...$_);
}