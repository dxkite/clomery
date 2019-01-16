<?php
namespace dxkite\user\response\setting;

use dxkite\support\file\File;
use dxkite\user\table\UserTable;
use dxkite\user\exception\UserException;
use dxkite\user\provider\AdminUserProvider;

class EditResponse extends \dxkite\support\setting\Response
{



    /**
     * 编辑用户
     *
     * @acl user.edit
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $provider = new AdminUserProvider;
        $userId=request()->get('id', 0);

        if (!visitor()->powerCompare($userId)) {
            $this->onDeny($context);
            return false;
        }

        if ($user = $provider->get($userId)) {
            $view->set('user', $user);
            if (request()->hasPost()) {
                try {
                    $file=request()->files('avatar');
                    if ($file && $file['error'] === 0) {
                        $file= File::createFromPost('avatar');
                        $provider->setAvatar($userId, $file);
                    }
                    
                    $edit = [];
                    $name =request()->post('name');
                    $email =request()->post('email');
                    $mobile = request()->post('mobile');
                    $password =request()->post('password', null);
              
                    if ($name && strtolower($name) != strtolower($user['name'])) {
                        $edit['name'] = $name;
                    }
                    if ($email && strtolower($email) != strtolower($user['email'])) {
                        $edit['email'] = $email;
                    }
                    if ($mobile && strtolower($mobile) != strtolower($user['mobile'])) {
                        $edit['mobile'] = $mobile;
                    }
                    if ($password) {
                        $edit['password'] = $password;
                    }
                    if (count($edit)) {
                        $provider->edit($user['id'], $edit);
                    }
                } catch (UserException $e) {
                    switch ($e->getCode()) {
                    case UserException::NAME_FORMAT_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
                            'title'=>__('用户名修改失败'),
                            'message'=>__('用户名格式错误')
                        ]);
                    break;
                    case UserException::NAME_EXISTS_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
                            'title'=>__('用户名修改失败'),
                            'message'=>__('用户名已被占用')
                        ]);
                    break;
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
                        'title'=>__('修改用户信息失败'),
                        'message'=>__($e->getMessage())
                    ]);
                }
                }
                hook()->exec('dxkite.user.response.setting.Edit.response', [$userId,$this,$view]);
            }
            if ($user = $provider->get($userId)) {
                $view->set('user', $user);
            }
        } else {
            $view->set('invaildId', true);
        }
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':setting/edit');
    }
}
