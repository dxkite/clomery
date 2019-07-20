<?php
namespace clomery\content\parser\parser;

use clomery\content\parser\ContextSetterTrait;
use HTMLPurifier_Config;
use suda\framework\filesystem\FileSystem;

/**
 * HTML过滤
 */
class HtmlParser implements \clomery\content\parser\Parser
{
    use ContextSetterTrait;

    public function toHtml(string $content):string
    {
        $dataPath = static::$context->getConfig()->get('data_path');
        $cachePath = $dataPath.'/cache/html_purifier';
        FileSystem::make($cachePath);
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $purifier = new \HTMLPurifier($config);
        $content = $purifier->purify($content);
        return $content;
    }
}
