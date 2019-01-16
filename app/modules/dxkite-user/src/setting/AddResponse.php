<?php
namespace dxkite\user\response\setting;

use dxkite\support\file\{File};
use dxkite\user\exception\UserException;
use dxkite\user\provider\AdminUserProvider;

class AddResponse extends \dxkite\support\setting\Response
{

    /**
     * 添加用户
     *
     * @acl user.add
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        if (request()->hasPost()) {
            $name =request()->post('name');
            $email =request()->post('email', null);
            $mobile = request()->post('mobile', null);
            $password =request()->post('password');
            $provider = new AdminUserProvider;
            if ($name && $password) {
                try {
                    $userId= $provider->add($name, $email, $mobile, $password);
                    hook()->exec('dxkite.user.response.setting.Add.response', [$userId,$this,$view]);
                    $file=request()->files('avatar');
                    if ($file && $file['error'] == 0) {
                        $file= File::createFromPost('avatar');
                        $provider->setAvatar($userId, $file);
                    }
                    $view->set('error', [
                        'type'=>'success',
                        'message'=>__('添加用户成功')
                    ]);
                } catch (UserException $e) {
                    switch ($e->getCode()) {
                        case UserException::NAME_FORMAT_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
                            'message'=>__('用户名格式错误')
                        ]);
                    break;
                    case UserException::NAME_EXISTS_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
                            'message'=>__('用户名已被占用')
                        ]);
                    break;
                    case UserException::EMAIL_FORMAT_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
                            'message'=>__('邮箱格式错误')
                        ]);
                    break;
                    case UserException::EMAIL_EXISTS_ERROR:
                        $view->set('error', [
                            'type'=>'danger',
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
                        'title'=>__('添加用户失败'),
                        'message'=>__($e->getMessage())
                    ]);
                    }
                }
            }
        }
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':setting/add');
    }
}
