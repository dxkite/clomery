<?php
namespace cn\atd3\response\admin\category;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\article\proxyobject\CategoryProxy;
use cn\atd3\proxy\Proxy;

class ListResponse  extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $call=new Proxy(new CategoryProxy($context));

        $page=$this->page('dxkite/article:admin/category/list');
        $max=$call->count();
        $now=$request->get()->page(1);
        if($request->get()->delete){
            $call->delete($request->get()->delete);
            return $this->refresh();
        }
        $page->set('title',__('分类浏览 第%d页',$now));
        $page->set('list',$call->getList($now,10));
        $page->set('page.max', ceil($max/10));
        $page->set('page.router', 'user:admin_group_list');
        $page->set('page.now', $now);
        return $page->render();
    }
}
