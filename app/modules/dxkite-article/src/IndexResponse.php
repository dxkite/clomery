<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\article\provider\ArticleProvider;
use dxkite\article\TemplateCSPLoader;

class IndexResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleProvider;
        $pageCurrent = request()->get('page',1);
        $articleData = $provider->getList(null,$pageCurrent);
        $page = $this->page('index');
        $page->set('title', 'dxkite 的博客');
        $page->set('articles', $articleData->getRows());
        $page->set('page',$articleData->getPage());
        return $page;
    }
}
