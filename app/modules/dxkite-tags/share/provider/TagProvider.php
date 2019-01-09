<?php
namespace dxkite\tags\provider;

use dxkite\tags\controller\TagController;

class TagProvider
{
    protected $name = '';
    protected $controller;

    public function __construct(string $name = null)
    {
        $this->controller =  new TagController($name);
    }

    public function addTags(int $target, array $tagsId)
    {
        return $this->controller->setTags($target,$tagsId);
    }

    public function search(string $tag):array {
        return $this->controller->search($tag);
    }

    public function getTags(int $target):?array
    {
        if ($tagrefs = $this->controller->getTagByRef($target)) {
            $tagsId = [];
            foreach ($tagrefs as $tag) {
                $tagsId[] = $tag['tag'];
            }
            return $this->controller->getTagByIds($tagsId);
        }
        return null;
    }
}
