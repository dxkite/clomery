<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\article\provider\ArticleProvider;
use dxkite\article\TemplateContentSecurityPolicyLoader;

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
