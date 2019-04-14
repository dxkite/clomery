<?php
namespace dxkite\content;

use Serializable;
use JsonSerializable;
use suda\framework\Request;
use suda\application\Application;
use dxkite\content\parser\HtmlParser;
use dxkite\content\parser\TextParser;
use dxkite\content\parser\MarkdownParser;
use support\openmethod\MethodParameterInterface;

class Content implements Serializable, JsonSerializable, MethodParameterInterface
{
    protected $type;

    protected $content;

    protected static $parser = [
        'html' => HtmlParser::class,
        'markdown' => MarkdownParser::class,
    ];


    public function __construct(string $content, string $type = 'markdown')
    {
        $this->type = $type;
        $this->content = $content;
    }

    public function raw()
    {
        return $this->content;
    }

    public function html()
    {
        $class = static::$parser[$this->type];
        $obj = new $class($this->content);
        return $obj->html();
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'raw' => $this->raw(),
            'html' => $this->html(),
        ];
    }

    public function serialize():string
    {
        return \json_encode([$this->type, $this->content]);
    }

    public function unserialize(string $serialized)
    {
        list($this->type, $this->content) = \json_decode($serialized);
        return $this;
    }

    /**
     * 创建对象
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param array|null $json
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, Application $application, Request $request, $json)
    {
        if ($from === 'JSON' && $json !== null) {
            if (\is_string($json)) {
                return new self($json);
            }
            return new self($json['content'] ?? '', $json['type'] ?? 'markdown');
        }
        if ($from === 'POST') {
            if ($request->hasPost($name)) {
                $type = $request->post($name.'_type', 'markdown');
                return new self($request->post($name), $type);
            }
        }
        return null;
    }
}
