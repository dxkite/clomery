<?php
namespace dxkite\support\response\role;

use dxkite\support\provider\GrantProvider;

class ListResponse extends \dxkite\support\setting\Response
{
    protected $provider;

    /**
     * 列出权限
     *
     * @acl role.list
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->provider = new GrantProvider;
        if (visitor()->hasPermission('role.delete')){
            if ($delete = request()->get('delete')) {
                $this->provider->deleteRole($delete);
                $this->refresh(true);
                return false;
            }
        }
        $page = request()->get('page', 1);
        $list = $this->provider->listRole();
        $pageBar = $list['page'];
        $pageBar['router'] = 'support:admin_role_list';
        $view->set('title', __('角色列表 第$0页', $list['page']['current']));
        $view->set('list', $list);
        $view->set('page', $pageBar);
    }

    public function adminContent($template)
    {
       $template->include('support:role/list');
    }
}
