<?php
namespace content\parser;

use suda\framework\filesystem\FileSystem;

class HtmlParser extends AbstractParser
{
    public function html()
    {
        $config = \HTMLPurifier_Config::createDefault();
        FileSystem::mkdir(SUDA_DATA.'/cache/html-content');
        $config->set('Cache.SerializerPath', SUDA_DATA.'/cache/html-content');
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $purifier = new \HTMLPurifier($config);
        $content = $purifier->purify($this->content);
        return $content;
    }
}
