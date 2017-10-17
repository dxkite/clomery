<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;

use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\article\proxyobject\ArticleProxy;
use cn\atd3\article\proxyobject\CategoryProxy;
use cn\atd3\proxy\Proxy;

class EditResponse extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('dxkite/article:admin/edit');
        
        $acall=new Proxy(new ArticleProxy($context));
        $ccall=new Proxy(new CategoryProxy($context));

        $id=$request->get("id");
        $categorys=$ccall->getList();
        $page->set('categorys', $categorys);
        $page->set('title', '编辑文章 -'.$id);
        $page->set('article', $acall->get($id));
        return $page->render();
    }
}
