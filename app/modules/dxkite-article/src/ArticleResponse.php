<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\response\Response;
use dxkite\article\provider\ArticleProvider;
use dxkite\article\TemplateCSPLoader;

class ArticleResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleProvider;
        $article = request()->get('article');
        if (\is_numeric($article)) {
            $articleData = $provider->getArticle($article);
        } else {
            $articleData  = $provider->getArticleBySlug($article);
        }
        if (\is_null($articleData)) {
            hook()->exec('suda:system:error::404');
            return;
        }
        $page = $this->page('post');
        $page->set('title', $articleData['title'].' | dxkite 的博客');
        $page->set('article', $articleData);
        return $page;
    }
}
