<?php
namespace dxkite\openuser\setting\response\user;

use suda\framework\Request;
use support\openmethod\Permission;
use support\openmethod\parameter\File;
use support\setting\exception\UserException;
use support\setting\response\SettingResponse;
use dxkite\openuser\setting\provider\UserProvider;

class EditResponse extends SettingResponse
{
    /**
     * 添加管理
     *
     * @acl open-user.edit
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $provider = new UserProvider;
        $provider->loadFromContext($this->context);
        $view = $this->view('user/edit');
        $id = $request->get('id');

        $user = $provider->getInfoById($id);
        $view->set('user', $user);

        if ($request->hasPost('name')) {
            $name = $request->post('name');
            $password = $request->post('password');
            $file = $request->getFile('avatar');
            $email = $request->post('email');
            $mobile = $request->post('mobile');
            $email = empty($email)?null:$email;
            $mobile = empty($mobile)?null:$mobile;
            $password = empty($password)?null:$password;

            if ($file->isValid()) {
                $file = new File($file);
            } else {
                $file = null;
            }
            try {
                $result = $provider->edit($id, $file,  $name, $password, $mobile, $email);
                $user = $provider->getInfoById($id);
                $view->set('user', $user);
                if ($result) {
                    $view->set('error', [
                        'type' => 'success',
                        'message' => '修改用户成功'
                    ]);
                } else {
                    $view->set('error', [
                        'type' => 'danger',
                        'message' => '修改用户失败'
                    ]);
                }
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::ERR_NAME_FORMAT:
                        $view->set('error', [
                            'type' => 'danger',
                            'message' => '用户名格式错误'
                        ]);
                    break;
                    case UserException::ERR_NAME_EXISTS:
                        $view->set('error', [
                            'type' => 'danger',
                            'message' => '用户名已被占用'
                        ]);
                    break;
                    case UserException::ERR_EMAIL_FORMAT:
                        $view->set('error', [
                            'type' => 'danger',
                            'message' => '邮箱格式错误'
                        ]);
                    break;
                    case UserException::ERR_EMAIL_EXISTS:
                        $view->set('error', [
                            'type' => 'danger',
                            'message' => '邮箱已被占用'
                        ]);
                    break;
                    case UserException::ERR_MOBILE_FORMAT:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => '修改失败',
                        'message' => '手机号格式错误'
                    ]);
                    break;
                    case UserException::ERR_MOBILE_EXISTS:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => '修改失败',
                        'message' => '手机号已被占用'
                    ]);
                    break;
                    default:
                    $view->set('error', [
                        'type' => 'danger',
                        'title' => '编辑用户失败',
                        'message' => $e->getMessage()
                    ]);
                }
            }
        }

        $view->set('title', '编辑管理');
        return $view;
    }
}
