<?php
namespace cn\atd3\response\article;

use suda\core\{Session,Cookie,Request,Query};

use cn\atd3\visitor\Context;


class ReadResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('article:article/read');
        $id=$request->get()->id(0);
        $article=table('article')->getArticle($id);
        $page->set('article',$article);
        $page->set('title','文章 - '.$article['title']);
        return $page->render();
    }
}
