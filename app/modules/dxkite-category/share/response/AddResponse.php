<?php
namespace dxkite\category\response;

use dxkite\category\response\BaseResponse;
use dxkite\category\provider\CategoryManageProvider;

class AddResponse extends BaseResponse
{
    public function contentAction($view)
    {
        $provider = new CategoryManageProvider($this->target);
        $request=request();


        $list = $provider->list();
        $view->set('category', $list);
        
        if ($request->hasPost()) {
            $id=$provider->add($request->post()->name, $request->post()->slug, $request->post()->parent);
            if ($id<=0) {
                $view->set('fail', false);
                $view->set('code', $id);
            } else {
                $this->go(u(app()->getActiveModule().':setting_category_list'));
                return false;
            }
        }
    }

    public function getTemplateName()
    {
        return 'add';
    }
}
