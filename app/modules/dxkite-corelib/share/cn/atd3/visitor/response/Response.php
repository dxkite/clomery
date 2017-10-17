<?php
namespace cn\atd3\visitor\response;

use suda\core\Request;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;
use cn\atd3\visitor\Permission;
use suda\core\Cookie;

abstract class Response extends \suda\core\Response
{
    private $context;

    public function onRequest(Request $request)
    {
        $context=Context::getInstance();
        $this->context=$context;
        $context->setRequest($request);
        $context->setVisitor($this->onVisitorCreate($context));
        if ($context->getVisitor()->canAccess([ $this,'onVisit'])) {
            $this->onVisit($context);
        } else {
            $this->onDeny($context);
        }
    }

    abstract public function onVisit(Context $context);
    abstract protected function onVisitorCreate(Context $context):Visitor;

    protected function createVisitor(string $classname):Visitor
    {
        $name=$this->context->getCookieName();
        if (Cookie::has($name)) {
            $visitor=new $classname(Cookie::get($name));
            debug()->trace(__('load_from_cookie %d:%s token %s', $visitor->getId(), $visitor->getToken(), $visitor->getMaskToken()));
        }else{
            $visitor=new $classname;
        }
        return $visitor;
    }

    public function onDeny(Context $context)
    {
        echo '<h1>deny access</h1>';
    }

    public function getContext()
    {
        return $this->context;
    }
}
