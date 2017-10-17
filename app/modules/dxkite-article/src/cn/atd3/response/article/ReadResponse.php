<?php
namespace cn\atd3\response\article;

use suda\core\{Session,Cookie,Request,Query};

use cn\atd3\visitor\Context;
use cn\atd3\article\view\Article;

class ReadResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page(conf('module.views.read','article:article/read'));
        $id=$request->get()->id(0);
        $article=(new Article($context))->getArticle($id);
        $page->set('article',$article);
        $page->set('title','文章 - '.$article['title']);
        return $page->render();
    }
}
