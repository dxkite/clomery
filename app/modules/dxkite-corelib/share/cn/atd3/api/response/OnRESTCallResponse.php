<?php
namespace cn\atd3\api\response;
use cn\atd3\visitor\response\RESTCallResponse;
use cn\atd3\visitor\Context;
use cn\atd3\visitor\Visitor;

abstract class OnRESTCallResponse extends RESTCallResponse
{
    protected function onVisitorCreate(Context $context):Visitor
    {
         return $this->createVisitor('cn\atd3\user\User');
    }
}