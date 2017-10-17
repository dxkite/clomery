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
class EditResponse  extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $call=new Proxy(new CategoryProxy($context));

        $page=$this->page('dxkite/article:1.0.0:admin/category/edit');
        $id=$request->get("id");
        if ($request->hasPost()) {
            $call->update($id,[
                'name'=>$request->post()->name,
                'slug'=>$request->post()->slug,
                'parent'=>$request->post()->parent
            ]);
        }

        $edit=$call->get($id);
        if (!$edit) {
            $page->set('invaild_id', true);
        }
        $page->set('title',__('编辑分类 - %d - %s', $id,$edit['name']));
        $page->set('edit', $edit);
        $page->set('category', $call->getList());
        return $page->render();
    }
}
