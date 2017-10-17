<?php

namespace cn\atd3\response\article\category;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\visitor\Context;
use cn\atd3\article\view\Article;

class CategoryResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $request=$context->getRequest();
        $view=new Article($context);
        $value=$view->getCategorys();
        $page=$this->page('dxkite/article:1.0.0:article/category');
        $page->set('title', 'Categorys')
        ->set('categorys', $value);
        return $page->render();
    }
}
