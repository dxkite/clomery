<?php
namespace clomery\article;

use Serializable;
use JsonSerializable;
use suda\framework\Request;
use suda\application\Application;
use clomery\article\parser\HtmlParser;
use clomery\article\parser\MarkdownParser;
use support\openmethod\MethodParameterBag;
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

    public function unserialize($serialized)
    {
        list($this->type, $this->content) = \json_decode($serialized);
    }

    /**
     * 创建参数
     *
     * @param integer $position
     * @param string $name
     * @param string $from
     * @param \support\openmethod\MethodParameterBag $bag
     * @return mixed
     */
    public static function createParameterFromRequest(int $position, string $name, string $from, MethodParameterBag $bag)
    {
        $json = $bag->getJson();
        if ($from === 'JSON' && $json !== null) {
            if (\is_string($json)) {
                return new self($json);
            }
            return new self($json['content'] ?? '', $json['type'] ?? 'markdown');
        }
        
        if ($from === 'POST') {
            $request = $bag->getRequest();
            if ($request->hasPost($name)) {
                $type = $request->post($name.'_type', 'markdown');
                return new self($request->post($name), $type);
            }
        }
        return null;
    }
}
