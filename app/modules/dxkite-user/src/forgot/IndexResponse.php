<?php
namespace dxkite\user\response\forgot;

use dxkite\user\HumanCode;
use dxkite\user\sms\SMSFactory;
use dxkite\support\visitor\Context;
use dxkite\user\controller\UserController;
use dxkite\support\visitor\response\Response;

/**
 * 生成验证码
 */
class IndexResponse extends Response
{
    public function onVisit(Context $context)
    {
        if (SMSFactory::isAvailable()) {
            $type = request()->get('type', 'mobile');
            if ($type == 'mobile') {
                $this->byMobile();
            } else {
                $this->byEmail();
            }
        } else {
            $this->byEmail();
        }
    }

    public function byMobile()
    {
        $view = $this->page('forgot/index');
        $view->set('type', 'mobile');

        if (request()->hasPost()) {
            $mobile = request()->post('mobile');
            $code = request()->post('code');

            $view->set('mobile', $mobile);
            if ($mobile && $code) {
                if (!HumanCode::check($code)) {
                    $view->set('invaildCode', true);
                } else {
                    $return = (new UserController)->sendPasswordVerifyMobile($mobile);
                    if (is_null($return)) {
                        $view->set('invaildMobile', '该账户不存在');
                    } elseif ($return) {
                        $this->go(u('forgot_reset'));
                        session()->set('reset_type', 'mobile');
                        session()->set('reset_value', $mobile);
                    } else {
                        $view->set('sendError', true);
                    }
                }
            } else {
                $view->set('invaildInput', true);
            }
        }
        $view->render();
    }

    public function byEmail()
    {
        $view = $this->page('forgot/email');
        $view->set('mobileAvailable', SMSFactory::isAvailable());
        $view->set('type', 'email');
        if (request()->hasPost()) {
            $email = request()->post('email');
            $code = request()->post('code');
            $view->set('email', $email);
            if ($email && $code) {
                if (!HumanCode::check($code)) {
                    $view->set('invaildCode', true);
                } else {
                    $return = (new UserController)->sendPasswordVerifyEmail($email);
                    if (is_null($return)) {
                        $view->set('invaildEmail', '该账户不存在');
                    } elseif ($return) {
                        $this->go(u('forgot_reset'));
                        session()->set('reset_type', 'email');
                        session()->set('reset_value', $email);
                    } else {
                        $view->set('sendError', true);
                    }
                }
            } else {
                $view->set('invaildInput', true);
            }
        }
        $view->render();
    }
}
