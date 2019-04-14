<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\openmethod\parameter\File;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;

class HomeSettingResponse extends UserSignResponse
{
    public function onUserVisit(Request $request)
    {
        $view = $this->view('home/setting');
        $user = $this->visitor->getAttributes();
        $view->set('user', $user);
        $provider = new UserProvider;
        $provider->loadFromContext($this->context);
        $view->set('title', '个人信息设置');
        if ($this->request->get('edit') == 'base') {
            $this->editBase($provider, $view, $user);
        }
       
        if ($this->request->get('edit') == 'password') {
            $this->editPassword($provider, $view);
        }
        return $view;
    }

    public function editPassword(UserProvider $provider, $view)
    {
        $check = $this->request->post('check');
        $password = $this->request->post('password');
        $repeat = $this->request->post('repeat');

        if ($check && $password == $repeat) {
            try {
                $provider->password($check, $password);
                $view->set('error', [
                    'type' => 'success',
                    'message' => __('修改密码成功')
                ]);
                $this->jumpForward();
            } catch (UserException $e) {
                $view->set('error', [
                    'type' => 'danger',
                    'title' => __('修改密码失败'),
                    'message' => __('原密码错误')
                ]);
            }
        } elseif ($password != $repeat) {
            $view->set('error', [
                'type' => 'danger',
                'title' => __('修改密码失败'),
                'message' => __('两次密码不相同')
            ]);
        }
    }
    
    public function editBase(UserProvider $provider, $view, $user)
    {
        $email = $this->request->post('email');
        $mobile = $this->request->post('mobile');
        $email = empty($email)?null:$email;
        $mobile = empty($mobile)?null:$mobile;
        $file = $this->request->getFile('avatar');

        if ($email || $mobile) {
            try {
                if ($email && strtolower($email) != strtolower($user['email'])) {
                    $email = $email;
                } else {
                    $email = null;
                }
                if ($mobile && strtolower($mobile) != strtolower($user['mobile'])) {
                    $mobile = $mobile;
                } else {
                    $mobile = null;
                }
                if ($file->isValid()) {
                    $file = new File($file);
                } else {
                    $file = null;
                }
                $provider->edit($file, null, $mobile, $email);
                $this->jumpForward();
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::ERR_EMAIL_FORMAT:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => __('邮箱修改失败'),
                        'message' => __('邮箱格式错误')
                    ]);
                    break;
                    case UserException::ERR_EMAIL_EXISTS:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => __('邮箱修改失败'),
                        'message' => __('邮箱已被占用')
                    ]);
                    break;
                    case UserException::ERR_MOBILE_FORMAT:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => __('修改失败'),
                        'message' => __('手机号格式错误')
                    ]);
                    break;
                    case UserException::ERR_MOBILE_EXISTS:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => __('修改失败'),
                        'message' => __('手机号已被占用')
                    ]);
                    break;
                    default:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => __('修改失败'),
                        'message' => __($e->getMessage())
                    ]);
                }
            }
        }
    }
}
