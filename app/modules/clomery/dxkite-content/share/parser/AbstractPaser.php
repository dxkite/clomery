<?php
namespace dxkite\content\parser;


abstract class AbstractParser {

    /**
     * 内容
     *
     * @var string
     */
    protected $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public function raw() { 
        return $this->content;
    }

    abstract function html();
}