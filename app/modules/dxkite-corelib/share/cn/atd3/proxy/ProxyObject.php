<?php
namespace cn\atd3\proxy;

use cn\atd3\visitor\Context;
use cn\atd3\visitor\exception\PermissionExcepiton;


abstract class ProxyObject
{
    protected $context;
    
    public function __construct(Context $context=null)
    {
        $this->context=$context??Context::getInstance();
    }
    
    public function getContext()
    {
        return $this->context=Context::getInstance();
    }

    public function getUserId()
    {
        $visitor=$this->getContext()->getVisitor();
        if ($visitor->isGuest()) {
            throw new PermissionExcepiton(__('permission deny: unsupport guest to run this'), 1);
        }
        return $visitor->getId();
    }

    public function hasPermission($p)
    {
        return  $this->getContext()->getVisitor()->hasPermission($p);
    }
}
