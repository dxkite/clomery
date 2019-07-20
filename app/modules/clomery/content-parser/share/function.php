<?php

use \clomery\content\parser\Content;

/**
 * 直接转换成HTML
 *
 * @param string $content
 * @param string $type
 * @return string
 */
function html_content(string $content, string $type = 'text'): string
{
    return (new Content($content, $type))->toHtml();
}

/**
 * 打包内容
 *
 * @param string|Content $content
 * @param string $type
 * @return string
 */
function content_pack($content, string $type = 'text'): string
{
    if ($content instanceof Content) {
        return Content::pack($content);
    }
    return Content::pack(new Content($content, $type));
}


/**
 * 创建一个内容
 *
 * @param string $content
 * @param string $type
 * @return Content
 */
function content_create(string $content, string $type = 'text'): Content
{
    return new Content($content, $type);
}

/**
 * 解包内容
 *
 * @param string $data
 * @return Content
 */
function content_unpack(string $data): ?Content
{
    return Content::unpack($data);
}

/**
 * 解包直接输出HTML
 *
 * @param Content|string|null $data
 * @return string
 */
function content_unpack2html($data): string
{
    if ($data) {
        if ($data instanceof Content) {
            $content = $data;
        } else {
            $content = Content::unpack($data);
        }
        if ($content instanceof Content) {
            return $content->toHtml();
        }
    }
    return strval($data);
}

/**
 * @param Content|string|null $data
 * @param int|null $length
 * @return string
 */
function content_unpack2text($data, ?int $length = null): string
{
    if ($data !== null) {
        if ($data instanceof Content) {
            $content = $data;
        } else {
            $content = Content::unpack($data);
        }
        if ($content instanceof Content) {
            return $content->toText($length);
        }
    }
    return strval($data);
}

/**
 * 解包直接输出数据源
 *
 * @param Content|string|null $data
 * @return string
 */
function content_unpack2raw($data): string
{
    if ($data !== null) {
        if ($data instanceof Content) {
            $content = $data;
        } else {
            $content = Content::unpack($data);
        }
        if ($content instanceof Content) {
            return $content->raw();
        }
    }
    return strval($data);
}


/**
 * 判断内容是不是一个正常包
 *
 * @param string $data
 * @return bool
 */
function content_ispack(string $data)
{
    return Content::isContent($data);
}
