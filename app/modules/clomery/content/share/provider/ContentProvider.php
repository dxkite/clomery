<?php


namespace clomery\content\provider;


use clomery\content\controller\CategoryController;
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

    /**
     * @var CategoryController
     */
    protected $categoryController;

    public function __construct(Table $content, Table $category, Table $tag, Table $relate)
    {
        $this->controller = new ContentController($content, $tag, $relate);
        $this->categoryController = new CategoryController($category);
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
                return $this->controller->getTagController()->getTags($idArray, ['id', 'name', 'slug']);
           }
        ]);
        $data = PageUtil::parseKeyToKey($data, 'category', [
            'category' => function ($categoryArray) {
                return $this->categoryController->getCategoryArray($categoryArray, ['id', 'name', 'slug']);
            }
        ]);
        return $data;
    }

    /**
     * @param string $article
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function getArticle(string $article) {
        $data = $this->controller->getArticle($article);
        if ($data !== null) {
            $data['tag'] = $this->controller->getTagController()->getTags($data['id'], ['id', 'name', 'slug', 'description', 'image']);
            $data['category'] = $this->categoryController->getCategory(strval($data['category']), ['id', 'name', 'slug', 'description', 'image']);
            list($previous, $next) =$this->controller->getNearArticle($data['id'], ['id', 'title', 'slug', 'description', 'image']);
            $data['near'] = [
                'previous' => $previous,
                'next' => $next,
            ];
        }
        return $data;
    }
}