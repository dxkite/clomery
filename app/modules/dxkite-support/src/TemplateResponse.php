<?php
namespace dxkite\support\response;

use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;
use dxkite\support\provider\TemplateProvider;

class TemplateResponse extends \dxkite\support\setting\Response
{
    protected $templateProvider;
    /**
     * 查看模板设置
     * @acl template.list
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->templateProvider =  new TemplateProvider;
        $request=$context->getRequest();
        if ($request->hasGet('template')) {
            $themeId= $request->get('template', 'default');
            $this->templateProvider->set($themeId);
            $this->go(u(static::$name));
        } elseif ($request->hasGet('delete')) {
            $this->templateProvider->delete($request->get('delete'));
            $this->go(u(static::$name));
        } elseif ($request->hasGet('unset')) {
            setting_set('template', 'default');
            $this->go(u(static::$name));
        }
        $list= $this->templateProvider->list();
        $view->set('title', __('模板管理'));
        $view->set('list', $list);
        $view->set('modules', json_encode(app()->getModules()));
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':template');
    }
}
