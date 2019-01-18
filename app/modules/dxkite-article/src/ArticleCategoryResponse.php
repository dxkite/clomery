<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\article\TemplateCSPLoader;
use dxkite\article\provider\ArticleProvider;
use dxkite\article\provider\ArticleCategoryProvider;
use dxkite\support\visitor\response\Response;

class ArticleCategoryResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleCategoryProvider;
        $articleProvider = new ArticleProvider;
        $slug = request()->get('slug');
        $pageCurrent = request()->get('page',1);
        $category = $provider->getBySlug($slug);
        $articles = $articleProvider->getList($category['id'],$pageCurrent);
        $page = $this->page('article-category');
        $page->set('category',$category);
        $page->set('title', '分类 '.$category['name'].' |  dxkite 的博客');
        $page->set('articles', $articles->getRows());
        $page->set('page',$articles->getPage());
        return $page;
    }
}
