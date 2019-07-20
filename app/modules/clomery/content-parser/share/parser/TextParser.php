<?php
namespace clomery\content\parser\parser;

use clomery\content\parser\ContextSetterTrait;
use clomery\content\parser\Parser;

/**
 * Text 转换成 HTML
 */
class TextParser implements Parser
{
    use ContextSetterTrait;

    protected static $space = null;
    protected static $space_html = '&nbsp;';

    public function __construct()
    {
        if (is_null(self::$space)) {
            self::$space = self::getTab2Space();
        }
    }

    public function toHtml(string $content):string
    {
        $content = htmlspecialchars($content);
        $content = preg_replace(['/ /','/\t/'], [static::$space_html,static::$space], $content);
        $array = preg_split('/\r?\n/', $content);
        $content = '';
        foreach ($array as $key => $value) {
            $content .= '<p>'.$value.'</p>';
        }
        return $content;
    }

    public static function getTab2Space()
    {
        $size = static::$context->getConfig()->get('content.tag_size', 4);
        $space = '';
        for ($i=0;$i<$size;$i++) {
            $space.= self::$space_html;
        }
        return $space;
    }
}
