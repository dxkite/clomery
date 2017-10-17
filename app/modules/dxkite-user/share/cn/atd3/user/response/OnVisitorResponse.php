<?php
namespace cn\atd3\user\response;

use cn\atd3\visitor\response\Response;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnVisitorResponse extends Response
{

    protected function onVisitorCreate(Context $context):Visitor
    {
         return $this->createVisitor(conf('visitorClass','cn\atd3\user\User'));
    }

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

    public function onGuestVisit(Context $context)
    {
    }
    
    public function onUserVisit(Context $context)
    {
    }
}
