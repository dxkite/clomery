<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\article\provider\ArticleCategoryProvider;
use dxkite\article\TemplateCSPLoader;

class CategoryResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleCategoryProvider;
        $categorys = $provider->getList();
        $page = $this->page('category');
        $page->set('title',__('分类列表'). '| dxkite 的博客');
        $page ->set('categorys', $categorys->getRows());
        return $page;
    }
}
