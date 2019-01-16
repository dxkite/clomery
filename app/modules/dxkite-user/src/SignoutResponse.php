<?php
namespace dxkite\user\response;

use dxkite\support\visitor\response\Response;
use dxkite\support\visitor\Context;
use dxkite\user\controller\UserController;

class SignoutResponse extends JumpBaseResponse
{
    public function onUserVisit(Context $context)
    {
        visitor()->signout();
        self::_jumpForward();
    }

    public function onGuestVisit(Context $context)
    {
        self::_jumpForward();
    }
}
