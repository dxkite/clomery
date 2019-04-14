<?php
namespace dxkite\content\parser;

class MarkdownParser extends AbstractParser
{
    public function html()
    {
        $parser = new \HyperDown\Parser;
        return $parser->makeHtml($this->content);
    }
}
