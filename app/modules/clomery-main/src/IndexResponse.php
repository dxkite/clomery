<?php
namespace dxkite\clomery\main\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\clomery\main\provider\ArticleProvider;
use dxkite\clomery\main\TemplateContentSecurityPolicyLoader;

class IndexResponse extends Response
{
    use TemplateContentSecurityPolicyLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleProvider;
        $pageCurrent = request()->get('page',1);
        $articleData = $provider->getList(null,$page);
        $page = $this->page('index');
        $page->set('menu',[
            '首页' => u('index'),
        ]);
        $page->set('socialLinks',[
            'Github' => 'https://github.com/dxkite'
        ]);
        $page->set('title', 'dxkite 的博客');
        $page->set('articles', $articleData->getRows());
        $page->set('page',$articleData->getPage());
        return $page;
    }
}
