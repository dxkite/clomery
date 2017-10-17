<?php
namespace cn\atd3\response\admin\group;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class AddResponse extends OnUserVisitorResponse
{
    /**
     * 添加分组
     * @acl add_group
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('dxkite/user:admin/group/add');
        $dao=new GroupDAO;
        if ($request->hasPost()) {
            $id=$dao->add($request->post()->name, array_keys($request->post()->auths([])));
            switch ($id) {
                case GroupDAO::INVAILD_NAME:
                    $page->set('invaild_name', true);
                    break;
                default:
                return $this->go(u('user:admin_group_list'));
            }
        }
        $page->set('title',__('添加分组'));
        $auths=$context->getVisitor()->getPermission()->readPermissions();
        $page->set('auths', $auths);
        return $page->render();
    }
}
