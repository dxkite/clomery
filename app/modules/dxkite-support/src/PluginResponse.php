<?php
namespace dxkite\support\response;

use dxkite\support\plugin\Manager;
use dxkite\support\visitor\Context;
use dxkite\support\provider\PluginProvider;
use dxkite\support\table\setting\SettingTable;

class PluginResponse extends \dxkite\support\setting\Response
{
    protected $pluginProvider;
    /**
     * 查看模板设置
     * @acl plugin
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->pluginProvider= new PluginProvider;
        if ($id = request()->get('delete')) {
            $this->pluginProvider->delete($id);
            $this->refresh();
            return false;
        } elseif ($id = request()->get('deactivate')) {
            $this->pluginProvider->deactivate($id);
            $this->refresh();
            return false;
        } elseif ($id = request()->get('active')) {
            $this->pluginProvider->active($id);
            $this->refresh();
            return false;
        }
        $list=$this->pluginProvider->list();
        $view->set('list', $list);
        $view->set('title', __('插件管理'));
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':plugin');
    }
}
