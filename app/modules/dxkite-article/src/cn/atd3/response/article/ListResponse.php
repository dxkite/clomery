<?php
namespace cn\atd3\response\article;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\article\view\Article;
use cn\atd3\visitor\Context;

class ListResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $request=$context->getRequest();
        $article=(new Article($context));
        $page=$this->page('dxkite/article:article/list');
        $page->set('title','文章列表');
        $page_num=$request->get()->page(1);
        $articles=$article->getList($page_num);
        // $page->set('page.max',ceil($count/10));
        // $page->set('page.now', $page_num);
        // $page->set('page.router','article:list');
        $page->set('list', $articles);
        return $page->render();
    }
}
