<?php


namespace clomery\content\provider;


use clomery\content\controller\ContentController;
use clomery\content\PageUtil;
use suda\application\database\Table;
use support\openmethod\PageData;

class ContentProvider
{
    /**
     * @var ContentController
     */
    protected $controller;

    public function __construct(Table $content, Table $tag, Table $relate)
    {
        $this->controller = new ContentController($content, $tag, $relate);
    }

    /**
     * @param string|null $search
     * @param string|null $category
     * @param array|null $tags
     * @param int|null $page
     * @param int $count
     * @param int $field
     * @param int $order
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getArticleList(?string $search, ?string $category, ?array $tags, ?int $page, int $count, int $field = 0, int $order = 0):PageData
    {
        $data = $this->controller->getArticleList($search, $category, $tags, $page, $count, $field, $order);
        $data = PageUtil::parseKeyToColumn($data, 'id', [
           'tag' => function($idArray) {
                return $this->controller->getTagController()->getTags($idArray);
           }
        ]);
        return $data;
    }
}