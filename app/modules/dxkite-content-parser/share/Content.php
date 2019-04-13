<?php
namespace dxkite\content\parser;

use dxkite\support\visitor\response\MethodParameter;

/**
 * 内容打包工具
 * 将内容包装化，并实时生成HTML
 *
 * @example
 * ```php
 * $text = "- 运行环境\r\n    - Windows x86 | Windows x64\r\n    - PHP 7.2.x\r\n    - MySQL | MariaDB 数据库\r\n    - Apache 2.x\r\n- 框架要求\r\n    - 版本 1.2.15 以及以上\r\n	\r\n	\r\n![img](router://localhost/support:upload?id=28)\r\n\r\n[router](router://localhost/index)";
 * $obj = new \dxkite\content\parser\Content($text,'markdown');
 * $pack_str =  $obj->pack($obj);
 * var_dump($pack_str);
 * var_dump($obj->isContent($pack_str));
 * var_dump($obj->unpack($pack_str));
 * ```
 */
class Content implements \JsonSerializable, MethodParameter
{
    const MD   = 'Markdown';
    const HTML = 'Html';
    const TEXT = 'Text';
    const MAGIC= "\x06\x02";

    protected $content;
    protected $type;
    protected $class;

    public function __construct(string $content, string $type)
    {
        $this->type = strtolower($type);
        $class = __NAMESPACE__.'\\parser\\'.self::TEXT . 'Parser';
        if ($this->type == strtolower(self::TEXT)) {
            $class = __NAMESPACE__.'\\parser\\'.self::TEXT . 'Parser';
        } elseif ($this->type == strtolower(self::MD) ||  $this->type == 'md') {
            $class = __NAMESPACE__.'\\parser\\'.self::MD . 'Parser';
            $this->type ='markdown';
        } elseif ($this->type == strtolower(self::HTML)) {
            $class = __NAMESPACE__.'\\parser\\'.self::HTML . 'Parser';
        }
        $this->class = $class;
        $this->content = $content;
    }

    public function raw():string
    {
        return $this->content;
    }

    /**
     * 转换成HTML
     * 使用了文件缓存
     * @return string
     */
    public function toHtml():string
    {
        $key = __CLASS__.'.content.'.md5($this->content);
        $class = new $this->class;
        if (conf('debug') || !cache()->has($key)) {
            $content=$class->decodeUrl($this->content);
            $html = $class->toHtml($content, $this->type);
            cache()->set($key, $html);
        }
        return cache()->get($key);
    }

    /**
     * 转化成字符串
     *
     * @param integer $length
     * @return string
     */
    public function toText(?int $length=null):string
    {
        $html = $this->toHtml();
        $text = strip_tags($html);
        if ($length) {
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
    public static function pack(Content $content):string
    {
        $text=(new $content->class)->encodeUrl($content->content);
        $md5 = md5($text);
        return Content::MAGIC.$content->type.','. $md5 .','.$content->class.','.$text;
    }

    /**
     * 解包
     *
     * @param string $content
     * @return Content|null
     */
    public static function unpack(string $content):?Content
    {
        if (self::isContent($content)) {
            $class = (new \ReflectionClass(Content::class))->newInstanceWithoutConstructor();
            $content = substr($content, 2);
            list($class->type, $md5, $class->class, $class->content) = explode(',', $content, 4);
            if ($md5 === md5($class->content)) {
                $class->content =  (new $class->class)->decodeUrl($class->content);
                return $class;
            }
        }
        return null;
    }
    
    /**
     * 判断是否为打包字符
     *
     * @param string $content
     * @return boolean
     */
    public static function isContent(string $content)
    {
        return strlen($content) > 2 &&  substr($content, 0, 2)  === Content::MAGIC;
    }

    public static function html(string $content, string $type)
    {
        return (new self($content, $type))->toHtml();
    }

    public static function createFromJson($jsonData):?object
    {
        if (\is_array($jsonData) && \array_key_exists('type', $jsonData) &&  \array_key_exists('content', $jsonData)) {
            return new Content($jsonData['content'], $jsonData['type']);
        }
        if (\is_string($jsonData)) {
            return new Content($jsonData, Content::MD);
        }
        return null;
    }
    
    public static function createFromPost(string $name, $postData):?object {
        return self::createFromJson($postData);
    }

    public function jsonSerialize()
    {
        return [
            'type'=> $this->type,
            'raw'=>$this->content,
            'html'=>$this->toHtml()
        ];
    }
}
