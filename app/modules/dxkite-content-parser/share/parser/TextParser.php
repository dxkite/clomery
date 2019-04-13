<?php
namespace dxkite\content\parser\parser;

/**
 * Text 转换成 HTML
 */
class TextParser implements \dxkite\content\parser\Parser
{

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
        $size = conf('content.parser.text.tab2space', 4);
        $space = '';
        for ($i=0;$i<$size;$i++) {
            $space.= self::$space_html;
        }
        return $space;
    }

    public function encodeUrl(string $content):string
    {
        return preg_replace_callback('/(https?\:\/\/\S+)/', function ($matchs) {
            if ($url=router()->encode($matchs[1])) {
                return $url;
            }
            return $matchs[1];
        }, $content);
    }

    public function decodeUrl(string $content):string
    {
        return preg_replace_callback('/(router\:\/\/\S+)/', function ($matchs) {
            if ($url=router()->decode($matchs[1])) {
                return $url;
            }
            return $matchs[1];
        }, $content);
    }
}
