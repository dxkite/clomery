<?php
namespace dxkite\user\response;

use dxkite\user\HumanCode;
use dxkite\user\sms\SMSFactory;
use dxkite\support\visitor\Context;
use dxkite\user\exception\UserException;
use dxkite\user\controller\UserController;

class SignupResponse extends JumpBaseResponse
{
    public function onGuestVisit(Context $context)
    {
        if (SMSFactory::isAvailable()) {
            $type = request()->get('type', 'mobile');
            if ($type == 'mobile') {
                $this->sign('mobile');
            } else {
                $this->sign('email');
            }
        } else {
            $this->sign('email');
        }
    }

    public function onUserVisit(Context $context)
    {
        if (is_null(session()->get('send_type', null))) {
            parent::onUserVisit($context);
        } else {
            $this->check();
        }
    }

    protected function check()
    {
        $view = $this->page('check');
        if (request()->hasPost('code')) {
            $code=request()->post('code');
            if ($code) {
                $controller = new UserController;
                $user = get_user_id();
                if ($controller->verifyCodeById($user, $code)) {
                    $controller->setVerifyed($user, session()->get('send_type'));
                    session()->delete('send_type');
                    self::_jumpForward();
                } else {
                    $view->set('invaildCode', true);
                }
            } else {
                $view->set('invaildCode', true);
            }
        }
        $view->render();
    }

    protected function sign(string $type)
    {
        $view=$this->page('signup');
        if (request()->hasPost()) {
            $name=request()->post('name');
            $mobile=request()->post('mobile');
            $email=request()->post('email');
            $password=request()->post('password');
            $repeat=request()->post('repeat');
            $code=request()->post('code');

            $view->set('name', $name);
            $view->set('email', $email);
            $view->set('mobile', $mobile);
            
            if ($name && $password == $repeat) {
                if (!HumanCode::check($code)) {
                    $view->set('invaildCode', true);
                    $view->set('checkField', true);
                }
                
                if ($type == 'email') {
                    if (empty($email)) {
                        $view->set('checkField', true);
                    }
                } else {
                    if (empty($mobile)) {
                        $view->set('checkField', true);
                    }
                }

                if ($view->get('checkField', false) != true) {
                    try {
                        $ctr = new UserController;
                        if ($type == 'email') {
                            $id = $ctr->add($name, $email, null, $password);
                            if ($id) {
                                if ($ctr->sendVerifyEmail($id)) {
                                    $ctr->signin($name, $password);
                                    session()->set('send_type', $ctr::TYPE_EMAIL);
                                } else {
                                    self::_jumpForward();
                                }
                            }
                        } else {
                            $id = $ctr->add($name, null, $mobile, $password);
                            if ($id) {
                                if ($ctr->sendVerifyMessage($id)) {
                                    $ctr->signin($name, $password);
                                    session()->set('send_type', $ctr::TYPE_MOBILE);
                                } else {
                                    self::_jumpForward();
                                }
                            }
                        }
                        $this->refresh();
                    } catch (UserException $e) {
                        switch ($e->getCode()) {
                            case UserException::NAME_FORMAT_ERROR:
                            $view->set('invaildName', '用户名格式错误');
                            break;
                            case UserException::NAME_EXISTS_ERROR:
                            $view->set('invaildName', '用户名已存在');
                            break;
                            case UserException::EMAIL_FORMAT_ERROR:
                            $view->set('invaildEmail', '邮箱格式错误');
                            break;
                            case UserException::EMAIL_EXISTS_ERROR:
                            $view->set('invaildEmail', '邮箱已存在');
                            break;
                            case UserException::MOBILE_FORMAT_ERROR:
                            $view->set('invaildMobile', '手机号格式错误');
                            break;
                            case UserException::MOBILE_EXISTS_ERROR:
                            $view->set('invaildMobile', '手机号已经被注册');
                            break;
                        }
                    }
                }
            } else {
                $view->set('passwordError', true);
            }
        }
        $view->set('mobileAvailable', SMSFactory::isAvailable());
        $view->set('type', $type);
        $view->render();
    }
}
