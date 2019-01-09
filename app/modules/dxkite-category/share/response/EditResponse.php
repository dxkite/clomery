<?php
namespace dxkite\category\response;

use dxkite\category\provider\CategoryManageProvider;

class EditResponse extends BaseResponse
{
    public function contentAction($view)
    {
        $provider = new CategoryManageProvider($this->target);
        $id = request()->get('id', 1);
        if (request()->hasPost()) {
            $provider->edit($id, [
                'name'=>request()->post()->name,
                'slug'=>request()->post()->slug,
                'parent'=>request()->post()->parent
            ]);
        }
        $view->set('edit', $provider->get($id));
    }

    public function getTemplateName()
    {
        return 'edit';
    }
}
