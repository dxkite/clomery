<?php
namespace cn\atd3\response\user;
use cn\atd3\api\response\OnCallableResponse;
use cn\atd3\user\UserProxy;

class Response extends OnCallableResponse
{
    public function getExportMethods($class=NULL)
    {
        return parent::getExportMethods(new UserProxy($this->getContext()));
    }
}
