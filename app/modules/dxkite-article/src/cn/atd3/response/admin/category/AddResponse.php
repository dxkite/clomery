<?php
namespace cn\atd3\response\admin\category;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;

use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\article\proxyobject\CategoryProxy;
use cn\atd3\proxy\Proxy;

class AddResponse  extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $call=new Proxy(new CategoryProxy($context));
        $page=$this->page('dxkite/article:admin/category/add');
        if ($request->hasPost()) {
            $id=$call->add($request->post()->name,$request->post()->slug,$request->post()->parent);
            if ($id<=0) {
                $page->set('fail', false);
                $page->set('code', $id);
            } else {
                $this->go(u('article:admin_category_list'));
            }
        }
        $page->set('title',__('添加分类'));
        $page->set('category', $call->getList());
        return $page->render();
    }
}
