<?php
namespace cn\atd3\response\user;

use cn\atd3\user\UserProxy;
use cn\atd3\user\response\OnVisitorResponse;
use cn\atd3\visitor\Context;

class SignupResponse extends OnVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $this->go(u('user:index'));
    }

    public function onGuestVisit(Context $context)
    {
        $request=$context->getRequest();
        $action=new UserProxy($context);
        if ($request->hasPost() && isset($request->post()->user) &&  isset($request->post()->password) && isset($request->post()->email)) {
            if ($request->post()->password!==$request->post()->retype) {
                $page=$this->page('dxkite/user:user/signup');
                $page->set('password_error', true);
                $page->set('signup_code', $action->getNeedSignCode(UserProxy::SIGN_UP));
                $page->set('user', $request->post()->user);
                $page->set('email', $request->post()->email);
                return $page->render();
            }
            $result=$action->signup($request->post()->user, $request->post()->email, $request->post()->password, $request->post()->code??'');
            // 注册成功
            if ($result>0) {
                return $this->go($request->get()->from(u('user:index')));
            } else {
                $page=$this->page('dxkite/user:user/signup');
                $page->set('user', $request->post()->user);
                $page->set('email', $request->post()->email);
                $page->set('signup_code', $action->getNeedSignCode(UserProxy::SIGN_UP));
                switch ($result) {
                    case UserProxy::EXISTS_USER: $page->set('exist_user', true);break;
                    case UserProxy::INVALID_CODE:$page->set('invaild_code', true);break;
                    case UserProxy::EXISTS_EMAIL:$page->set('exist_email', true);break;
                }
                return $page->render();
            }
        } else {
            $page=$this->page('dxkite/user:user/signup');
            $page->set('signup_code',$action->getNeedSignCode(UserProxy::SIGN_UP));
            return $page->render();
        }
    }
}
