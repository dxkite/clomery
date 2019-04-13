<?php
namespace dxkite\content\parser\parser;

/**
 * Markdown转换成 HTML, 使用 HyperDown
 */
class MarkdownParser extends \HyperDown\Parser implements \dxkite\content\parser\Parser
{
    // parseLink
    public function toHtml(string $content):string
    {
        return parent::makeHtml($content);
    }

    public function encodeUrl(string $content):string
    {
        // 图片 URL ![]() []()
        $content = preg_replace_callback('/(\!)?\[(.*?)\]\((https?\:\/\/.+?)\)/',function ($matchs) {
            if (isset($matchs[3]) && $url=router()->encode($matchs[3])) {
                return $matchs[1].'['.$matchs[2].']('.$url.')';
            }
            return $matchs[0];
        }, $content);
        // [id][link]
        $content = preg_replace_callback('/\[(.*?)\]\((https?\:\/\/.+?)\)/',function ($matchs) {
            if (isset($matchs[2]) && $url=router()->encode($matchs[2])) {
                return '['.$matchs[1].']['.$url.']';
            }
            return $matchs[0];
        }, $content);
        // 原始URL
        return preg_replace_callback('/(\'|")(https?\:\/\/\S+)((?(1)\1))/', function ($matchs) {
            if ($url=router()->encode($matchs[2])) {
                return $matchs[1].$url.$matchs[3];
            }
            return $matchs[0];
        }, $content);
    }

    public function decodeUrl(string $content):string {
         // 图片 URL ![]() []()
         $content = preg_replace_callback('/(\!)?\[(.*)?\]\((router\:\/\/.+?)\)/',function ($matchs) {
            if (isset($matchs[3]) && $url=router()->decode($matchs[3])) {
                return $matchs[1].'['.$matchs[2].']('.$url.')';
            }
            return $matchs[0];
        }, $content);
        // [id][link]
        $content = preg_replace_callback('/\[(.*)?\]\((router\:\/\/.+?)\)/',function ($matchs) {
            if (isset($matchs[2]) && $url=router()->decode($matchs[2])) {
                return '['.$matchs[1].']['.$url.']';
            }
            return $matchs[0];
        }, $content);
        // 原始URL
        return preg_replace_callback('/(router\:\/\/\S+)/', function ($matchs) {
            if ($url=router()->decode($matchs[1])) {
                return $url;
            }
            return $matchs[1];
        }, $content);
    }
}
