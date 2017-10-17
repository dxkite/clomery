<?php
namespace cn\atd3\response\user;

use suda\core\Request;
use cn\atd3\user\UserProxy;
use cn\atd3\user\response\OnVisitorResponse;
use cn\atd3\visitor\Context;

class SigninResponse extends OnVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $this->go(u('user:index'));
    }

    public function onGuestVisit(Context $context)
    {
        $request=$context->getRequest();
        $action=new UserProxy($context);
        if ($request->hasPost() && isset($request->post()->account) && isset($request->post()->password)) {
            
            $result=$action->signin($request->post()->account, $request->post()->password, isset($request->post()->remember), $request->post()->code??'');
            // 登陆成功
            if ($result>0) {
                $url=$request->get()->from(u('user:index'));
                return $this->go($url);
            } else {
                $page=$this->page('dxkite/user:user/signin');
                $page->set('signin_code',  $action->getNeedSignCode($action::SIGN_IN));
                switch ($result) {
                    case UserProxy::INVALID_ACCOUNT:$page->set('invaild_account', true);break;
                    case UserProxy::INVALID_CODE:$page->set('invaild_code', true);break;
                    case UserProxy::INVALID_PASSWORD:$page->set('invaild_password', true);break;
                }
                return $page->render();
            }
        } else {
            $page=$this->page('dxkite/user:user/signin');
            $page->set('signin_code',  $action->getNeedSignCode($action::SIGN_IN));
            return $page->render();
        }
    }
}
