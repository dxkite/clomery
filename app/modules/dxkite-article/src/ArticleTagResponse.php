<?php
namespace dxkite\article\response;

use dxkite\support\visitor\Context;
use dxkite\article\TemplateCSPLoader;
use dxkite\article\provider\ArticleTagProvider;
use dxkite\support\visitor\response\Response;

class ArticleTagResponse extends Response
{
    use TemplateCSPLoader;

    public function onVisit(Context $context)
    {
        $provider = new ArticleTagProvider;
        $tagName = request()->get('tag');
        $pageCurrent = request()->get('page',1);
        $tag = $provider->getTagByName($tagName);
        $articles = $provider->getArticleByTag($tag['id'],$pageCurrent);
        $page = $this->page('tag-article');
        $page->set('tag',$tag);
        $page->set('title', '标签 '.$tagName.' |  dxkite 的博客');
        $page ->set('articles', $articles->getRows());
        return $page;
    }
}
