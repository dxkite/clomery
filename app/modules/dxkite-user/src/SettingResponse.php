<?php
namespace dxkite\user\response;

use dxkite\support\visitor\response\VisitorResponse;
use dxkite\support\visitor\Context;
use dxkite\user\controller\UserController;
use dxkite\user\exception\UserException;
use dxkite\support\file\File;

class SettingResponse extends VisitorResponse
{
    protected $user;

    public function onGuestVisit(Context $context)
    {
        if ($userId=request()->get('id')) {
            $this->info($userId);
        } else {
            $this->go(u('signin'));
            cookie()->set('redirect_uri', u($_GET));
            echo __('正在跳转登陆');
        }
    }
    
    public function onUserVisit(Context $context)
    {
        $view=$this->page('home/setting');
        $userId = get_user_id();
        if ($user=(new UserController)->get($userId)) {
            $this->user =$user;
            $view->set('user', $user);
        }

        if (request()->get('edit') == 'base') {
            $this->editBase($view);
        }
       
        if (request()->get('edit') == 'password') {
            $this->editPassword($view);
        }
        $view->render();
    }

    public function editPassword($view)
    {
        $check =request()->post('check');
        $password =request()->post('password');
        $repeat =request()->post('repeat');

        if ($check && $password == $repeat) {
            try {
                (new UserController)->resetPassword(get_user_id(), $check, $password);
                $view->set('error', [
                    'type'=>'success',
                    'message'=>__('修改密码成功')
                ]);
                $this->go(u());
            } catch (UserException $e) {
                $view->set('error', [
                    'type'=>'danger',
                    'title'=>__('修改密码失败'),
                    'message'=>__('原密码错误')
                ]);
            }
        } elseif ($password != $repeat) {
            $view->set('error', [
                'type'=>'danger',
                'title'=>__('修改密码失败'),
                'message'=>__('两次密码不相同')
            ]);
        }
    }
    
    public function editBase($view)
    {
        $file=request()->files('avatar');
        $userCtr = new UserController;

        if ($file && $file['error'] == 0) {
            $file= File::createFromPost('avatar');
            $userCtr->setAvatar($this->user['id'], $file);
        }

        $email =request()->post('email');
        $mobile =request()->post('mobile');

        if ($email || $mobile) {
            try {
                $edit =[];
                if ($email && strtolower($email) != strtolower($this->user['email'])) {
                    $edit['email'] = $email;
                }
                if ($mobile && strtolower($mobile) != strtolower($this->user['mobile'])) {
                    $edit['mobile'] = $mobile;
                }
                if (count($edit)) {
                    $userCtr->edit($this->user['id'], $edit);
                }
                $this->go(u());
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::EMAIL_FORMAT_ERROR:
                    $view->set('error', [
                        'type'=>'danger',
                        'title'=>__('邮箱修改失败'),
                        'message'=>__('邮箱格式错误')
                    ]);
                    break;
                    case UserException::EMAIL_EXISTS_ERROR:
                    $view->set('error', [
                        'type'=>'danger',
                        'title'=>__('邮箱修改失败'),
                        'message'=>__('邮箱已被占用')
                    ]);
                    break;
                    case UserException::MOBILE_FORMAT_ERROR:
                    $view->set('error', [
                        'type'=>'danger',
                        'title'=>__('修改失败'),
                        'message'=>__('手机号格式错误')
                    ]);
                    break;
                    case UserException::MOBILE_EXISTS_ERROR:
                    $view->set('error', [
                        'type'=>'danger',
                        'title'=>__('修改失败'),
                        'message'=>__('手机号已被占用')
                    ]);
                    break;
                    default:
                    $view->set('error', [
                        'type'=>'danger',
                        'title'=>__('修改失败'),
                        'message'=>__($e->getMessage())
                    ]);
                }
            }
        }
    }
}
