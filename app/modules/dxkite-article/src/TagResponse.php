<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\article\provider\ArticleTagProvider;
use dxkite\article\TemplateCSPLoader;

class TagResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleTagProvider;
        $tags = $provider->getList();
        $page = $this->page('tag');
        $page->set('title',__('标签列表'). '| dxkite 的博客');
        $page ->set('tags', $tags->getRows());
        return $page;
    }
}
