<?php
namespace cn\atd3\response\user;

use cn\atd3\user\UserProxy;
use cn\atd3\user\response\OnVisitorResponse;
use cn\atd3\visitor\Context;

class SignoutResponse extends OnVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        (new UserProxy($context))->signout();
        $this->forward();
    }
    
    public function onGuestVisit(Context $context)
    {
        $this->go(u('user:signup'));
    }
}
