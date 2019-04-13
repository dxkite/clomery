<?php
namespace dxkite\content\parser\parser;

/**
 * HTML过滤
 */
class HtmlParser implements \dxkite\content\parser\Parser
{
    public function toHtml(string $content):string
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', storage()->path(CACHE_DIR.'/html_purifier'));
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $purifier = new \HTMLPurifier($config);
        $content = $purifier->purify($content);
        return $content;
    }

    public function encodeUrl(string $content):string
    {
        $content = preg_replace_callback('/<img(.+?)src="(https?\:\/\/.+?)"(.*?)\/?>/',function ($matchs) {
            if (isset($matchs[2])) {
                if ($url=router()->encode($matchs[2])) {
                    return '<img'.$matchs[1].'src="'.$url.'"'.$matchs[3].'>';
                }
            }
            return $matchs[0];
        }, $content);
        return preg_replace_callback('/<a(.+?)href="(https?\:\/\/.+?)"(.*?)>/', function ($matchs) {
            if ($url=router()->encode($matchs[2])) {
                return '<a'.$matchs[1].'href="'.$url.'"'.$matchs[3].'>';
            }
            return $matchs[0];
        }, $content);
    }

    public function decodeUrl(string $content):string {
        $content = preg_replace_callback('/<img(.+?)src="(router\:\/\/.+?)"(.*?)\/?>/',function ($matchs) {
            if (isset($matchs[2])) {
                if ($url=router()->decode($matchs[2])) {
                    return '<img'.$matchs[1].'src="'.$url.'"'.$matchs[3].'>';
                }
            }
            return $matchs[0];
        }, $content);
        return preg_replace_callback('/<a(.+?)href="(router\:\/\/.+?)"(.*?)>/', function ($matchs) {
            if ($url=router()->decode($matchs[2])) {
                return '<a'.$matchs[1].'href="'.$url.'"'.$matchs[3].'>';
            }
            return $matchs[0];
        }, $content);
    }
}
