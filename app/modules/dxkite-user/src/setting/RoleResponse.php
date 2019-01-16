<?php
namespace dxkite\user\response\setting;

use dxkite\user\table\UserTable;
use dxkite\support\visitor\Context;
use dxkite\support\provider\GrantProvider;
use dxkite\support\table\visitor\RoleTable;
use dxkite\user\provider\AdminUserProvider;

class RoleResponse extends \dxkite\support\setting\Response
{

    /**
     * 编辑用户权限
     *
     * @acl user.role
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $userId=request()->get('id', 0);
        $provider = new AdminUserProvider;
        $granter = new GrantProvider;

        if ($user= $provider->get($userId)) {
            $view->set('user', $user);
            $role=(new RoleTable)->list();
            $view->set('roles', $role??[]);

            if ($grant=request()->get('grant'))  {
                $granter->grant($grant, $userId);
                $this->refresh();
                return;
            }
            
            if ($grant=request()->get('revoke'))  {
                $granter->revoke($grant, $userId);
                $this->refresh();
                return;
            }

            $grants= $granter->listUserRoles($userId);
            $view->set('list', $grants);
        } else {
            $this->go(u(module(__FILE__).':admin_list'));
        }
    }

    public function adminContent($template)
    { 
        $template->include(module(__FILE__).':setting/role');
    }
}
