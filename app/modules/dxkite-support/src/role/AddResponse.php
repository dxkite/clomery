<?php
namespace dxkite\support\response\role;

use dxkite\support\visitor\Permission;
use dxkite\support\provider\GrantProvider;

class AddResponse extends \dxkite\support\setting\Response
{
    protected $provider;
    /**
     * 添加权限
     *
     * @acl role.create
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->provider = new GrantProvider;
        if (request()->hasPost('name')) {
            $id= $this->provider->createRole(request()->post('name'), new Permission(array_keys(request()->post('auths', []))));
            if ($id) {
                return $this->go(u('support:admin_role_list'));
            } else {
                $view->set('invaildName', true);
            }
        }
        $auths=$context->getVisitor()->getPermission()->readPermissions();
        $view->set('title', __('添加角色'));
        $view->set('auths', $auths);
    }

    public function adminContent($template)
    {
        $template->include('support:role/add');
    }
}
