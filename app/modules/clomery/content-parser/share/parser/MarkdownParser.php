<?php
namespace clomery\content\parser\parser;

use clomery\content\parser\ContextSetterTrait;
use HyperDown\Parser;

/**
 * Markdown转换成 HTML, 使用 HyperDown
 */
class MarkdownParser extends Parser implements \clomery\content\parser\Parser
{
    use ContextSetterTrait;

    // parseLink
    public function toHtml(string $content):string
    {
        return parent::makeHtml($content);
    }
}
