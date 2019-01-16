<?php
namespace dxkite\user\hook;

use suda\template\Manager as TemplateManger;
use dxkite\user\controller\UserController;

class ViewHook
{
    public static function settingNavbar($template)
    {
        $user=(new UserController)->get(get_user_id());
        $template->set('user', $user);
        $template->include(module(__FILE__).':setting/navbar');
    }
}
