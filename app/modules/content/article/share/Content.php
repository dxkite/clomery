<?php
namespace content\article;

use ReflectionException;
use Serializable;
use JsonSerializable;
use support\openmethod\MethodParameterBag;
use support\openmethod\MethodParameterInterface;
use support\setting\event\GlobalObject;

class Content implements Serializable, JsonSerializable, MethodParameterInterface
{
    protected $type;

    protected $content;

    public function __construct(string $content, string $type = 'markdown')
    {
        $this->type = $type;
        $this->content = $content;
    }

    public function raw()
    {
        return $this->content;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    protected function html() {
        return GlobalObject::$application->event()->process('content:to-html', $this->content, [$this->type]);
    }

    /**
     * @return array|mixed
     * @throws ReflectionException
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'raw' => $this->raw(),
            'html' => $this->html(),
        ];
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function serialize():string
    {
        $content = GlobalObject::$application->event()->process('content:serialize', $this->content, [$this->type]);
        return \json_encode([$this->type, $content]);
    }

    /**
     * @param string $serialized
     * @throws ReflectionException
     */
    public function unserialize($serialized)
    {
        list($this->type, $content) = \json_decode($serialized);
        $this->content= GlobalObject::$application->event()->process('content:unserialize', $content, [$this->type]);
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
