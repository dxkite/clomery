<?php
namespace dxkite\android\message\response\setting;

use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;
use dxkite\user\table\UserTable;

class ManagerResponse extends \dxkite\support\setting\Response
{
    public function onAdminView($view, $context)
    {
        return true;
    }

    public function adminContent($template)
    {
        \suda\template\Manager::include('android-message:setting/manager', $template)->render();
    }
}
