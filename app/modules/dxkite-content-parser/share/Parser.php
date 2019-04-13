<?php
namespace dxkite\content\parser;

interface Parser {
    public  function toHtml(string $content):string;    
    public function encodeUrl(string $content):string;
    public function decodeUrl(string $content):string;
}