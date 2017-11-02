<?php
namespace cn\atd3\api\response;
use cn\atd3\visitor\response\CallableResponse;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnCallableResponse extends CallableResponse
{
    protected function onVisitorCreate(Context $context):Visitor
    {
         return $context->loadVisitorFromCookie('cn\atd3\user\User');
    }
}