<?php
namespace cn\atd3\response\user;

use cn\atd3\response\SignResponse;
use suda\core\Request;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\user\UserProxy;


class HomeResponse extends OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $visitor=$context->getVisitor();
        $action=new UserProxy($context);
        $user=$action->getInfo();
        $page=$this->page('dxkite/user:user/index');
        $page->set('name', $user['name']);
        $page->set('email', $user['email']);
        return $page->render();
    }
}
