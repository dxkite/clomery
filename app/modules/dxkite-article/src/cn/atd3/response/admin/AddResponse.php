<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;

use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\article\proxyobject\CategoryProxy;
use cn\atd3\proxy\Proxy;


class AddResponse extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('article:admin/add');
        $category_call=new Proxy(new CategoryProxy($context));
        $categorys=$category_call->getList();
        $page->set('title', '创建文章');
        $page->set('categorys', $categorys);
        return $page->render();
    }
}
