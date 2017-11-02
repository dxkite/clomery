<?php
namespace cn\atd3\api\response;
use cn\atd3\visitor\response\RPCCallResponse;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnRPCCallResponse extends RPCCallResponse
{
    protected function onVisitorCreate(Context $context):Visitor
    {
         return $context->loadVisitorFromCookie('cn\atd3\user\User');
    }
}