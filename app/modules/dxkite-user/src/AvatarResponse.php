<?php
namespace dxkite\user\response;

use dxkite\support\visitor\response\Response;
use dxkite\support\visitor\Context;
use dxkite\user\controller\UserController;

class AvatarResponse extends Response
{
    public function onVisit(Context $context)
    {
        $userId=request()->get('id', 0 );
        $user=(new UserController)->get($userId);
        if ($user['avatar'] > 0) {
            $this->go(u('support:upload', ['id'=>$user['avatar']]));
        } else {
            $this->go(assets_url(module(__FILE__), 'img/avatar.png'));
        }
    }
}
