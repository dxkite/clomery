<?php
namespace cn\atd3\api\response;
use cn\atd3\visitor\response\RPCCallResponse;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnRPCCallResponse extends RPCCallResponse
{
    protected function onVisitorCreate(Context $context):Visitor
    {
         return $this->createVisitor('cn\atd3\user\User');
    }
}