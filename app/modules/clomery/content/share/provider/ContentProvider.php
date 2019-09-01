<?php


namespace clomery\content\provider;


use clomery\content\controller\ContentController;
use clomery\content\PageUtil;
use support\openmethod\PageData;
use support\visitor\provider\UserSessionAwareProvider;

class ContentProvider extends UserSessionAwareProvider
{
    /**
     * @var ContentController
     */
    protected $controller;

    /**
     * ContentProvider constructor.
     * @param ContentController $controller
     */
    public function __construct(ContentController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @open false
     * @return ContentController
     */
    public function getController(): ContentController
    {
        return $this->controller;
    }

    /**
     * @param string|null $search
     * @param string|null $category
     * @param array|null $tags
     * @param int|null $page
     * @param int $row
     * @param int $field
     * @param int $order
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getArticleList(?string $search, ?string $category, ?array $tags, ?int $page, int $row = 10, int $field = 0, int $order = 0):PageData
    {
        $data = $this->controller->getArticleList($search, $category, $tags, $page, $row, $field, $order);
        $data = PageUtil::parseKeyToColumn($data, 'id', [
           'tag' => function($idArray) {
                return $this->controller->getTagController()->getTags($idArray, ['id', 'name', 'slug']);
           }
        ]);
        $data = PageUtil::parseKeyToKey($data, 'category', [
            'category' => function ($categoryArray) {
                return $this->controller->getCategoryController()->getWithArray($categoryArray, ['id', 'name', 'slug']);
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
            $data['category'] = $this->controller->getCategoryController()->get(strval($data['category']), ['id', 'name', 'slug', 'description', 'image']);
            list($previous, $next) =$this->controller->getNearArticle($data['id'], ['id', 'title', 'slug', 'description', 'image']);
            $data['near'] = [
                'previous' => $previous,
                'next' => $next,
            ];
        }
        return $data;
    }

    /**
     * @param string $category
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function getCategory(string $category) {
        $data = $this->controller->getCategoryController()->get($category, ['id', 'name', 'slug', 'description', 'image' ]);
        return $data;
    }

    /**
     * @param string $tag
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function getTag(string $tag) {
        $data = $this->controller->getTagController()->get($tag, ['id', 'name', 'slug', 'description', 'image' ]);
        return $data;
    }

    /**
     * @param int $page
     * @param int $row
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getTagList(?int $page = null, int $row = 10) {
        $data = $this->controller->getTagController()->getList($page, $row);
        return $data;
    }

    /**
     * @param int|null $page
     * @param int $row
     * @return PageData
     * @throws \suda\database\exception\SQLException
     */
    public function getCategoryList(?int $page = null, int $row = 10) {
        $data = $this->controller->getCategoryController()->getList($page, $row);
        return $data;
    }
}