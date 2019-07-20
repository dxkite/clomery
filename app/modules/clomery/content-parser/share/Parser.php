<?php

namespace clomery\content\parser;

use suda\framework\Context;

interface Parser
{
    public static function setContext(Context $context): void;

    public function toHtml(string $content): string;
}