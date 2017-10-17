<?php
namespace cn\atd3\response\admin\group;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\response\AuthResponse;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class ListResponse extends OnUserVisitorResponse
{
    /**
     * 添加分组
     * @acl edit_group
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('dxkite/user:admin/group/list');
        $dao=new GroupDAO;
        $max=$dao->count();
        $now=$request->get()->page(1);
        if ($request->get()->delete) {
            $dao->deleteByPrimaryKey($request->get()->delete);
            $this->refresh(true);
            return;
        }
        $page->set('title', __('分组列表 第%d页', $now));
        $page->set('group.list', $dao->list($now, 10));
        $auths=$dao->getAuthList();
        $page->set('auths', $auths);
        $page->set('page.max', ceil($max/10));
        $page->set('page.router', 'user:admin_group_list');
        $page->set('page.now', $now);
        return $page->render();
    }
}
