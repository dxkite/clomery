<?php
namespace cn\atd3\user\response;

use cn\atd3\visitor\response\Response;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;
use cn\atd3\user\User;

abstract class OnVisitorResponse extends Response
{

    public function onVisit(Context $context)
    {
        if ($context->getVisitor()->isGuest()) {
            $this->onGuestVisit($context);
        } elseif ($context->getVisitor()->canAccess([$this,'onUserVisit'])) {
            $this->onUserVisit($context);
        } else {
            $this->onDeny($context);
        }
    }

    protected function onVisitorCreate(Context $context):Visitor
    {
         return $context->loadVisitorFromCookie(User::class);
    }

    public function onGuestVisit(Context $context)
    {
    }
    
    public function onUserVisit(Context $context)
    {
    }
}
