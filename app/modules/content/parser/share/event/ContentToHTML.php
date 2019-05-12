<?php
namespace content\parser\event;

use content\parser\HtmlParser;
use content\parser\MarkdownParser;

class ContentToHTML {
    public static function parse(string $content, string $type) {
        if (strcasecmp($type, 'markdown') === 0 ) {
            return (new MarkdownParser($content))->html();
        }
        return (new HtmlParser($content))->html();
    }
}