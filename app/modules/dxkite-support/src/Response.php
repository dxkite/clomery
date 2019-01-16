<?php
namespace dxkite\support\response;

use dxkite\support\visitor\Context;
use dxkite\support\provider\SettingProvider;

class Response extends \dxkite\support\setting\Response
{
    /**
     * 列出网站信息
     * 
     * @acl website.info
     * 
     * @param Context $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $view->set('title', __('网站状态'));
        $view->set('env',(new SettingProvider)->getEnviroments());
    }

    public function adminContent($template) {
        $template->include(module(__FILE__).':index');
    }
}
