<?php
namespace cn\atd3\response\user;

use suda\core\Request;
use cn\atd3\user\response\OnVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\proxy\Proxy;
use cn\atd3\user\UserProxy;

class VerifyResponse extends OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $this->type('png');
        $this->noCache();
        (new Proxy(new UserProxy($context)))->displayImage();
    }
}
