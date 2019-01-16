<?php
namespace dxkite\user\response\forgot;

use dxkite\support\visitor\response\Response;
use dxkite\support\visitor\Context;
use dxkite\user\HumanCode;
use dxkite\user\controller\UserController;

/**
 * 生成验证码
 */
class ResetResponse extends Response
{
    public function onVisit(Context $context)
    {
        $view = $this->page('forgot/reset');
        $value = session()->get('reset_value');
        $type = session()->get('reset_type');

        if (request()->hasPost()) {
            $password=request()->post('password');
            $repeat=request()->post('repeat');
            $code=request()->post('code');

            if ($code && $value && $password == $repeat) {
                $controller = new UserController;
                if ($type == 'email') {
                    $email = $value;
                    if ($controller->verifyCodeByEmail($email, $code)) {
                        if ($controller->checkPasswordByEmail($email, $password)) {
                            $view->set('passwordConfirm', true);
                        } else {
                            if ($controller->setPasswordByEmail($email, $password)) {
                                $view->set('resetSuccess', true);
                                session()->delete('reset_value');
                                session()->delete('reset_type');
                                $controller->cleanVerifyCodeByEmail($email);
                            } else {
                                $view->set('resetError', true);
                            }
                        }
                    } else {
                        $view->set('invaildCode', true);
                    }
                } else {
                    $mobile = $value;
                    if ($controller->verifyCodeByMobile($mobile, $code)) {
                        if ($controller->checkPasswordByMobile($mobile, $password)) {
                            $view->set('passwordConfirm', true);
                        } else {
                            if ($controller->setPasswordByMobile($mobile, $password)) {
                                $view->set('resetSuccess', true);
                                session()->delete('reset_value');
                                session()->delete('reset_type');
                                $controller->cleanVerifyCodeByMobile($mobile);
                            } else {
                                $view->set('resetError', true);
                            }
                        }
                    } else {
                        $view->set('invaildCode', true);
                    }
                }
            } else {
                $view->set('passwordError', true);
            }
        }
        $view->render();
    }
}
