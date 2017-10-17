<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\Manager as um;

use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class ListResponse extends OnUserVisitorResponse
{
    /**
     * 编辑用户
     * @acl list_user,edit_user
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('user:admin/list');
        $dao=new UserDAO;
          if ($request->get()->active>0) {
            $dao->setStatus($request->get()->active, UserDAO::ACTIVE);
            return $this->refresh();
        }
        if ($request->get()->freeze>0) {
            $dao->setStatus($request->get()->freeze, UserDAO::FREEZE);
            return $this->refresh();
        }
        
        $now=$request->get()->page(1);
        if(isset($request->get()->search)){
            if($request->get()->type=='name'){
                $list= $dao->setFields(['id','name','email','group_id','signup_time','status'])->searchByName($request->get()->search);
            }else{
                $list= $dao->setFields(['id','name','email','group_id','signup_time','status'])->searchByEmail($request->get()->search);
            }
            $page->set('page.next',count($list)>=10);
            $page->set('title', __('用户列表 - 搜索结果 第%d页', $now));
        }else{
            $page->set('title', __('用户列表 第%d页', $now));
            $page->set('page.max', ceil($dao->count()/10));
            $list= $dao->setFields(['id','name','email','group_id','signup_time','status'])->list($now, 10);
        }
        $page->set('page.now', $now);
        $page->set('id2group',um::groups2name());

        $page->set('page.router', 'user:admin_list');
        $page->set('user.list', $list);
        return $page->render();
    }
}
