<?php

namespace clomery\content\parser;

use clomery\content\parser\parser\HtmlParser;
use clomery\content\parser\parser\MarkdownParser;
use clomery\content\parser\parser\RstTextParser;
use clomery\content\parser\parser\TextParser;
use ReflectionClass;
use suda\framework\Context;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;

/**
 * 内容打包工具
 * 将内容包装化，并实时生成HTML
 *
 * @example
 * ```php
 * $text = "- 运行环境\r\n    - Windows x86 | Windows x64\r\n    - PHP 7.2.x\r\n    - MySQL | MariaDB 数据库\r\n    - Apache 2.x\r\n- 框架要求\r\n    - 版本 1.2.15 以及以上\r\n	\r\n	\r\n![img](router://localhost/support:upload?id=28)\r\n\r\n[router](router://localhost/index)";
 * $obj = new \clomery\content\parser\Content($text,'markdown');
 * $pack_str =  $obj->pack($obj);
 * var_dump($pack_str);
 * var_dump($obj->isContent($pack_str));
 * var_dump($obj->unpack($pack_str));
 * ```
 */
class Content implements \JsonSerializable, MethodParameterInterface
{
    const MD = 'Markdown';
    const HTML = 'Html';
    const TEXT = 'Text';
    const RST = 'reStructuredText';

    const MAGIC = "\x06\x02";

    protected $content;
    protected $type;
    protected $class;
    protected $version = '0.1';

    /**
     * @var Context
     */
    private static $context;

    /**
     * @return Context
     */
    public static function getContext(): Context
    {
        return self::$context;
    }

    /**
     * @param Context $context
     */
    public static function setContext(Context $context): void
    {
        self::$context = $context;
    }

    public function __construct(string $content, string $type)
    {
        $this->type = strtolower($type);
        if ($this->type == strtolower(self::TEXT)) {
            $this->class = TextParser::class;
            $this->type = 'text';
        } elseif ($this->type == strtolower(self::RST) || $this->type == 'rst') {
            $this->class = RstTextParser::class;
            $this->type = 'rst';
        } elseif ($this->type == strtolower(self::MD) || $this->type == 'md') {
            $this->class = MarkdownParser::class;
            $this->type = 'md';
        } elseif ($this->type == strtolower(self::MD) || $this->type == 'md') {
            $this->type = 'html';
            $this->class = HtmlParser::class;
        } else {
            $this->class = '';
        }
        $this->content = $content;
    }

    public function raw(): string
    {
        return $this->content;
    }

    /**
     * 转换成HTML
     * 使用了文件缓存
     * @return string
     */
    public function toHtml(): string
    {
        $key = __CLASS__ . '.content.' . md5($this->content);
        if (strlen($this->class) == 0) {
            return $this->content;
        }
        /** @var Parser $class */
        $class = new $this->class;
        $debug = self::$context->getConfig()->get('debug');
        $cache = self::$context->getCache();
        $event = self::$context->getEvent();
        if ($debug || !$cache->has($key)) {
            $html = $class->toHtml($this->content);
            $cache->set($key, $html);
            $event->exec('content.output::html', [&$html]);
            return $html;
        }
        return $cache->get($key);
    }

    /**
     * 转化成字符串
     *
     * @param integer $length
     * @return string
     */
    public function toText(?int $length = null): string
    {
        $html = $this->toHtml();
        $text = strip_tags($html);
        if ($length > 0) {
            return mb_substr($text, 0, $length);
        }
        return $text;
    }

    /**
     * 打包
     *
     * @param Content $content
     * @return string
     */
    public static function pack(Content $content): string
    {
        $data = [
            'type' => $content->type,
            'content' => $content->content,
            'version' => '1.0',
        ];
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 解包
     *
     * @param string $content
     * @return Content|null
     */
    public static function unpack(string $content): ?Content
    {
        /** @var Content $class */
        if (self::isContent($content)) {
            try {
                $class = (new ReflectionClass(Content::class))->newInstanceWithoutConstructor();
            } catch (\ReflectionException $e) {
                return null;
            }
            $content = substr($content, 2);
            [$class->type, $md5, $class->class, $class->content] = explode(',', $content, 4);
            if ($md5 === md5($class->content)) {
                return $class;
            } else {
                $class->type = 'html';
                return $class;
            }
        }
        try {
            $data = json_decode($content, true);
            $obj = new static($data['content'], $data['type']);
            $obj->version = $data['version'];
            return $obj;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * 判断是否为打包字符
     *
     * @param string $content
     * @return boolean
     */
    public static function isContent(string $content)
    {
        return strlen($content) > 2 && substr($content, 0, 2) === Content::MAGIC;
    }

    public static function html(string $content, string $type)
    {
        return (new self($content, $type))->toHtml();
    }

    public static function createFromData($jsonData): ?object
    {
        if (\is_array($jsonData) && \array_key_exists('type', $jsonData) && \array_key_exists('content', $jsonData)) {
            return new Content($jsonData['content'], $jsonData['type']);
        }
        if (\is_string($jsonData)) {
            return new Content($jsonData, Content::MD);
        }
        return null;
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'raw' => $this->content,
            'version' => $this->version,
            'html' => $this->toHtml(),
        ];
    }

    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        if ($from === 'POST') {
            return static::createFromData($bag->getRequest()->post($name));
        }
        if ($from === 'JSON') {
            return static::createFromData($bag->getJson()[$name] ?? []);
        }
        return null;
    }
}
