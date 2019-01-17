<?php
namespace dxkite\clomery\main\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\clomery\main\provider\ArticleProvider;
use dxkite\clomery\main\TemplateContentSecurityPolicyLoader;

class ArticleResponse extends Response
{
    use TemplateContentSecurityPolicyLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleProvider;
        $articleId = request()->get('article');
        $articleData = $provider->getArticle($articleId);
        $page = $this->page('post');
        $page->set('title', $articleData['title'].' | dxkite 的博客');
        $page->set('article', $articleData);
        return $page;
    }
}
