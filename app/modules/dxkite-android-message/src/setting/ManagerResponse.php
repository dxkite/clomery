<?php
namespace dxkite\android\message\response\setting;

use dxkite\support\visitor\Context;
use dxkite\user\table\UserTable;
use  dxkite\android\message\Message;
use dxkite\support\file\File;

class ManagerResponse extends \dxkite\support\setting\Response
{
    public function onAdminView($view, $context)
    {
        if (request()->hasPost()) {
            if (request()->get()->action == 'color') {
                Message::editPull(
                    request()->post('message'),
                    request()->post('time', 5000),
                    request()->post('url'),
                    request()->post('color'),
                    request()->post('bgcolor')
                );
                Message:: enableMessage(request()->post('show', true));
                $this->refresh();
                return false;
            } elseif (request()->get()->action == 'image') {
                Message::editAds(
                    File::createFromPost('image'),
                    request()->post('url')
                );
            }
        }
        $message = setting('androidMessage');
        if ($message) {
            $view->set('message', $message);
        }
        $view->set('show', setting('androidMessageEnable'));
        return true;
    }

    public function adminContent($template)
    {
        \suda\template\Manager::include('android-message:setting/manager', $template)->render();
    }
}
