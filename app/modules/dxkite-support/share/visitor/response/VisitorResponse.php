<?php
namespace dxkite\support\visitor\response;

use dxkite\support\visitor\Context;
use dxkite\support\visitor\Visitor;

abstract class VisitorResponse extends Response
{
    final public function onVisit(Context $context)
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
        $route=config()->get('user-signin-route', null);
        cookie()->set('redirect_uri', u($_GET));
        if ($route && !request()->getMapping()->is($route)) {
            $this->go(u($route));
        } elseif ($url = config()->get('user_signin_url', null)) {
            $this->go($url);
        } else {
            debug()->warning('visitor do not has way to signin,you can config route by user-signin-route or url by user_signin_url, auto sign as user 1');
            visitor()->signin(1);
            $this->refresh();
        }
    }
    
    abstract public function onUserVisit(Context $context);
}
