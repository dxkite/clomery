<?php
namespace support\setting\response\role;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\VisitorProvider;
use support\setting\response\SettingResponse;

class AddResponse extends SettingResponse
{
    /**
     * 列出权限
     *
     * @acl setting:role.add
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $controller = new VisitorProvider;
        $controller->loadFromContext($this->context);
        if ($request->hasPost('name')) {
            $id = $controller->createRole($request->post('name'), array_keys($request->post('auths', [])));
            if ($id) {
                $this->goRoute('role_list');
                return;
            } else {
                $view->set('invaildName', true);
            }
        }
        $view = $this->view('role/add');
        $auths = $this->context->getVisitor()->getPermission()->readPermissions($this->context->getApplication());
        $view->set('title', $this->_('添加角色'));
        $view->set('auths', $auths);
        return $view;
    }
}
