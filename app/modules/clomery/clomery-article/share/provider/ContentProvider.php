<?php
namespace clomery\article\provider;

use clomery\article\DataUnit;
use clomery\article\data\ArticleData;
use clomery\article\controller\ArticleController;
use dxkite\category\controller\CategoryController;
use dxkite\openuser\provider\VisitorAwareProvider;


/**
 * 博客内容提供器
 */
class ContentProvider extends VisitorAwareProvider
{

    /**
     * 文章控制器
     * @var ArticleController
     */
    protected $article;

    /**
     * 分类控制器
     * @var CategoryController
     */
    protected $category;

    public function  __construct(?DataUnit $unit = null)
    {
        $this->article = new ArticleController($unit);
        $this->category = new CategoryController($unit);
    }

    /**
     * 获取文章列表
     *
     * @param int $page
     * @param int $row
     * @param string|null $category
     * @param array|null $tags
     * @param string|null $search
     * @return \support\setting\PageData
     */
    public function list(int $page = 1, int $row = 10, ?string $category = null, ?array $tags = null, ?string $search = null)
    {
        $user = $this->visitor->isGuest() ? null: $this->visitor->getId();
        if ($category !== null && is_numeric($category) == false) {
            $category = $this->category->getId($category);
        }
        return $this->article->getArticleList($user, $search, $category, $tags, $page, $row);
    }

    /**
     * 获取文章目录
     *
     * @param string $parent
     * @return array
     * @throws \suda\orm\exception\SQLException
     */
    public function index(string $parent) {
        return $this->article->index($parent);
    }


}
