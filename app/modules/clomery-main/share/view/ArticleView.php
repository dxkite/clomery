<?php
namespace dxkite\clomery\main\view;

use dxkite\support\view\PageData;
use dxkite\article\controller\ArticleController;
use dxkite\article\controller\ArticleCategoryController;

class ArticleView
{
    
    /**
     * 文章控制器
     *
     * @var ArticleController
     */
    protected $article;
    /**
     * 文章分类
     *
     * @var ArticleCategoryController
     */
    protected $category;

    public function __construct(ArticleController $article, ArticleCategoryController $category)
    {
        $this->article = $article;
        $this->category = $category;
    }

    public function listView(PageData $page):PageData
    {
        if ($page->getSize() > 0) {
            $rows = $page->getRows();
            $users = [];
            $categorys = [];
            $ids = [];
            foreach ($rows as $index => $row) {
                $ids[] = $row['id'];
                $users[] = $row['user'];
                $categorys[] = $row['category'];
            }
            $userInfo = get_user_public_info_array($users);
            $categoryInfo = $this->getCategorys($categorys);

            foreach ($rows as $index => $row) {
                $rows[$index]['user'] = $userInfo[$row['user']] ?? $row['user'];
                $rows[$index]['category'] = $categoryInfo[$row['category']];
            }
            $page->setRows($rows);
        }
        return $page;
    }

    protected function getCategorys(array $categorys):array
    {
        $categoryInfo = $this->category->getCategoryByIds($categorys);
        if (\is_null($categoryInfo)) {
            $categoryInfo =[];
        }
        $categoryInfo[0] = [
            'id' => 0,
            'category' => __('未分类'),
            'slug' => '',
        ];
        return $categoryInfo;
    }

    protected function getTags(array $tags):array
    {
        
    }
}
