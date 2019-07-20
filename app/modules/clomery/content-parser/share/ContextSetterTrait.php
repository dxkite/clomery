<?php


namespace clomery\content\parser;


use suda\framework\Context;

trait ContextSetterTrait
{
    /**
     * @var Context
     */
    protected static $context;

    /**
     * @param Context $context
     */
    public static function setContext(Context $context): void
    {
        self::$context = $context;
    }
}