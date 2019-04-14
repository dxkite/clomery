<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;


abstract class UserSignResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $this->goRoute('signin',['redirect_uri' => $request->getUrl()]);
    }
    
    abstract public function onUserVisit(Request $request);
}
