<?php
namespace dxkite\support\response;

use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;
use dxkite\support\table\setting\SettingTable;

class EmailResponse extends \dxkite\support\setting\Response
{
    /**
     * 设置邮件
     * 
     * @acl website.[setEmail,viewEmail]
     * 
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        if (setting('smtp')) {
            $smtp = setting('smtp');
            $smtp['passwd'] = '';
            $view->set('title', __('邮箱设置'));
            $view->set('smtp', $smtp);
            $view->set('smtp_enable', setting('smtp_enable',true));
        }
        if (request()->hasPost() && request()->post('smtp')) {
            setting_set('smtp', request()->post('smtp'));
            if (request()->post('smtp_enable')) {
                setting_set('smtp_enable', request()->post('smtp_enable') == 'true');
            }else{
                setting_set('smtp_enable', false);
            }
            $this->go(u());
        }
    }

    public function adminContent($template)
    {
        \suda\template\Manager::include('support:set-email', $template)->render();
    }
}
