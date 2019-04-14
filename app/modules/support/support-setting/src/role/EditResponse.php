<?php
namespace support\setting\response\role;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\VisitorProvider;
use support\setting\response\SettingResponse;

class EditResponse extends SettingResponse
{
    /**
     * 列出权限
     *
     * @acl setting:role.edit
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $controller = new VisitorProvider;
        $controller->loadFromContext($this->context);
        
        $id = $request->get('id');
        $role = $controller->getRole($id);
        $view = $this->view('role/edit');
        if ($role !== null) {
            $name = $request->post('name');
            if ($request->hasPost('auths')) {
                $controller->editRole($id, $request->post('name'), array_keys($request->post('auths', [])));
                $role = $controller->getRole($id);
            }
            $view->set('title', $this->_('编辑角色 $0', $name));
            $view->set('submenu', $this->_('编辑角色'));
            $auths = $this->visitor->getPermission()->readPermissions($this->application);
            $view->set('auths', $auths);
            $view->set('permission', $role['permission']);
            $view->set('name', $role['name']);
        } else {
            $view->set('invaildId', true);
        }
        return $view;
    }
}
