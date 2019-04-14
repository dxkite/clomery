<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\UserProvider;
use support\setting\exception\UserException;
use support\setting\provider\VisitorProvider;
use support\setting\response\SettingResponse;

class RoleResponse extends SettingResponse
{
    /**
     * 添加管理
     * 
     * @acl setting:user.role
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $provider = new UserProvider;
        $provider->loadFromContext($this->context);
        $visitorProvider = new VisitorProvider;
        $visitorProvider->loadFromContext($this->context);

        $view = $this->view('user/role');
        $id = $request->get('id');
        $user = $provider->getInfoById($id);
        $view->set('user', $user);

        if ($request->hasGet('grant')) {
            $visitorProvider->grant($request->get('grant'), $id);
            $this->goRoute('user_role',['id' => $id]);
            return;
        } elseif ($request->hasGet('revoke')) {
            $visitorProvider->revoke($request->get('revoke'), $id);
            $this->goRoute('user_role',['id' => $id]);
            return;
        }
        $list = $visitorProvider->listUserRole($id);
        $roles = $visitorProvider->listRole();

        $view->set('list', $list->getRows());
        $view->set('roles', $roles->getRows());
        $view->set('title', '编辑权限');
        return $view;
    }
}
