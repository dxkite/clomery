<?php
namespace dxkite\category\response;

use dxkite\category\provider\CategoryManageProvider;

class ListResponse extends BaseResponse
{
    public function contentAction($view)
    {
        $now = request()->get('page', 1);
        $provider = new CategoryManageProvider($this->target);
        
        if($id = request()->get()->delete){
            $provider->delete($id);
            $this->refresh();
            return false;
        }

        $list = $provider->list($now);
        $view->set('title', __('分类浏览 第$0页', $now));
        $view->set('category', $list);

        $view->set('page.max', $list['page']['max']);
        $view->set('page.router', app()->getActiveModule().':setting_category_list');
        $view->set('page.now', $now);
    }

    public function getTemplateName()
    {
        return 'list';
    }
}
