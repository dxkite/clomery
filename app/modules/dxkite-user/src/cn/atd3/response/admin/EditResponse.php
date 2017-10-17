<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class EditResponse extends OnUserVisitorResponse
{
    /**
     * 编辑用户
     * @acl edit_user
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('user:admin/edit');
        $id=$request->get('id');
        $user=(new UserDAO)->getInfo($id);
        if ($request->hasPost()) {
            if ($request->post()->password) {
                $info=(new UserDAO)->edit($id, $request->post()->name, $request->post()->email, $request->post()->group_id, $request->post()->password);
            } else {
                $info=(new UserDAO)->edit($id, $request->post()->name, $request->post()->email, $request->post()->group_id);
            }
            
            switch ($info) {
                case UserDAO::EXISTS_USER:
                    $page->set('invaild_name', true);
                    break;
                case UserDAO::EXISTS_EMAIL:
                    $page->set('invaild_email', true);
                    break;
                default:
                    $user=(new UserDAO)->getInfo($id);
            }
            $page->assign($user);
        } else {
            if ($user) {
                $page->assign($user);
            } else {
                $page->set('invaild_id', true);
            }
        }
        $page->set('title', __('修改用户 - %d', $id));
        $page->set('groups', (new GroupDAO)->list());
        return $page->render();
    }
}
