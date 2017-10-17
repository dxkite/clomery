<?php

namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\dao\PagesDAO;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class ListResponse extends OnUserVisitorResponse
{
    /**
     * 
     * @acl list_page,delete_page
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('pages:admin/list');
        $dao=new PagesDAO;
        if ($request->hasGet() && $request->get()->delete!=0){
            $dao->deleteByPrimaryKey($request->get()->delete);
            $this->forward();
            return;
        }
        $now=$request->get()->page(1);
        $list=$dao->list($now, 10);
        $page->set('page.max', ceil($dao->count()/10));
        $page->set('page.now', $now);
        $page->set('page.router', 'pages:admin_list');
        $page->set('list', $list);
        return $page->render();
    }
}
