<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\setting\response\Response;
use support\setting\provider\UserProvider;

class SignoutResponse extends Response
{
    public function onGuestVisit(Request $request)
    {
        $this->goRoute('index');
    }
    
    public function onAccessVisit(Request $request)
    {
        (new UserProvider)->signout($this->visitor->getId());
        $this->goRoute('index');
    }
}
