<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;


class SignoutResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $this->jumpForward();
    }
    
    public function onUserVisit(Request $request)
    {
        $provider = new UserProvider;
        $provider->signout($this->visitor->getId());
        $this->jumpForward();
    }
}
