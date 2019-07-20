<?php

namespace clomery\content\parser\event;

use clomery\content\parser\Content;
use suda\application\Application;
use suda\framework\Config;

class InvokeContext
{
    public static function invoke(Config $config, Application $application)
    {
        Content::setContext($application);
    }
}
