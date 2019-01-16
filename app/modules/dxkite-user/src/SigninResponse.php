<?php
namespace dxkite\user\response;

use dxkite\support\visitor\Context;

use dxkite\user\controller\UserController;
use dxkite\user\exception\UserException;
use dxkite\user\HumanCode;

class SigninResponse extends JumpBaseResponse
{
    public function onGuestVisit(Context $context)
    {
        $view=$this->page('signin');
        if ($url =request()->get('redirect_uri')) {
            cookie()->set('redirect_uri', $url);
        }
        if (request()->hasPost()) {
            $account=request()->post('account');
            $password=request()->post('password');
            $code=request()->post('code');
            $remember=request()->post('remember', false);
            if ($account && $password && $code) {
                if (HumanCode::check($code)) {
                    try {
                        (new UserController)->signin($account, $password, $remember);
                        self::_jumpForward();
                        session()->delete('change_user');
                    } catch (UserException $e) {
                        switch ($e->getCode()) {
                            case UserException::NAME_FORMAT_ERROR:
                            case UserException::EMAIL_FORMAT_ERROR:
                            case UserException::ACCOUNT_OR_PASSWORD_ERROR:
                            case UserException::ACCOUNT_IS_NOT_ACTIVE:
                                $view->set('invaildInput', true);
                        }
                    }
                } else {
                    $view->set('invaildCode', true);
                }
            } else {
                $view->set('invaildCode', true);
                $view->set('invaildInput', true);
            }
        }
        $view->render();
    }

    public function onUserVisit(Context $context)
    {
        if (request()->get('change_user')) {
            session()->set('change_user', true);
        }
        if (session()->has('change_user')) {
            $this->onGuestVisit($context);
        } else {
            parent::onUserVisit($context);
        }
    }
}
