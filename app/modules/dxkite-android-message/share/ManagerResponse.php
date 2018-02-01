<?php
namespace cn\atd3\android;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context; 

class ManagerResponse extends OnUserVisitorResponse
{
    /**
     * 
     * @acl android-message.[editPull,editAds]
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('android-message:manager');
        $page->set('title','Android 信息管理');
        return $page->render();
    }
}
