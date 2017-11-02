<?php
namespace cn\atd3\visitor\response;

use suda\core\Request;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;
use cn\atd3\visitor\Permission;


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

    public function onDeny(Context $context)
    {
        echo '<h1>deny access</h1>';
    }
    
    public function getContext()
    {
        return $this->context;
    }
}
