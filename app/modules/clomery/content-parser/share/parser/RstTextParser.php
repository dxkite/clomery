<?php


namespace clomery\content\parser\parser;


use clomery\content\parser\ContextSetterTrait;

class RstTextParser implements \clomery\content\parser\Parser
{
    use ContextSetterTrait;

    public function toHtml(string $content): string
    {
        $parser = new \Gregwar\RST\Parser;
        return $parser->parse($content);
    }
}