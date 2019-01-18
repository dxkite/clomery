<?php
namespace dxkite\article\provider;

use suda\archive\Table;
use dxkite\support\view\PageData;
use dxkite\article\controller\ArticleCategoryController;

class ArticleCategoryProvider
{

    /**
     * 标签
     *
     * @var ArticleCategoryController
     */
    protected $category;


    public function __construct(string $prefix='')
    {
        $this->category = new ArticleCategoryController($prefix);
    }
    
    /**
     * 获取分类列表
     *
     * @param integer|null $page
     * @param integer $count
     * @return PageData
     */
    public function getList(?int $page=null, int $count=10):PageData
    {
        $page = $this->category->getList($page, $count);
        return $page;
    }

    /**
     * 创建分类
     *
     * @acl article.write:category
     * @param string $name
     * @param string|null $slug
     * @param integer $parent
     * @return integer
     */
    public function add(string $name, ?string $slug = null, int $parent= 0):int
    {
        \visitor()->requirePermission('article.write:category');
        return $this->category->save($name, $slug, $parent);
    }

    public function getBySlug(string $slug):?array {
        return $this->category->getBySlug($slug);
    }
}
