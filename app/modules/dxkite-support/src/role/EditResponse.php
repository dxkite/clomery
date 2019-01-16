<?php
namespace dxkite\support\response\role;

use dxkite\support\visitor\Permission;
use dxkite\support\provider\GrantProvider;
use dxkite\support\table\visitor\RoleTable;

class EditResponse extends \dxkite\support\setting\Response
{
    protected $provider;
    /**
     * 编辑权限
     *
     * @acl role.edit
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $id=request()->get('id');
        $this->provider = new GrantProvider;
        if ($id) {
            if (request()->hasPost('auths')) {
                $permissions = array_keys(request()->post('auths', []));
                $this->provider->editRole($id, request()->post('name'), new Permission($permissions));
            }
            $info=(new RoleTable)->getByPrimaryKey($id);
            $view->set('name', $info['name']);
            $view->set('title', __('编辑角色：$0', $info['name']));
            $view->set('permission', $info['permission']);
            $auths=$context->getVisitor()->getPermission()->readPermissions();
            $view->set('auths', $auths);
        } else {
            $view->set('invaildId', true);
        }
    }
    public function adminContent($template)
    {
        $template->include('support:role/edit');
    }
}
