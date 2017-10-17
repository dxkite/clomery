<?php
namespace cn\atd3\api\response;
use cn\atd3\visitor\response\CallableResponse;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnCallableResponse extends CallableResponse
{
    protected function onVisitorCreate(Context $context):Visitor
    {
         return $this->createVisitor('cn\atd3\user\User');
    }
}