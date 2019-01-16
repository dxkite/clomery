<?php
namespace dxkite\support\setting;

use suda\core\Query;
use dxkite\support\visitor\Context;
use dxkite\support\provider\SettingProvider;

abstract class Response extends \dxkite\support\visitor\response\VisitorResponse
{
    protected $settingProvider;
    public function onUserVisit(Context $context)
    {
        if ($context->getVisitor()->canAccess([$this,'onAdminView'])) {
            $page=$this->page('support:setting-view');
            $this->settingProvider = new SettingProvider;
            $mapping=\suda\core\route\Mapping::current();

            $menu = $this->settingProvider-> getSettingMenu($mapping->getFullName());
            $page->set('menuTree', $menu);

            foreach ($menu as $value) {
                if ($value['select']) {
                    $page->set('title', $value['name']);
                    $page->set('menuName', $value['name']);
                }
            }
            
            if ($this->onAdminView($page, $context) !== false) {
                $page->render();
            }
        } else {
            $this->onDeny($context);
        }
    }

    abstract public function onAdminView($page, $context);
    abstract public function adminContent($template);
    public function onDeny(Context $context)
    {
        $this->page('support:deny')->render();
    }
}
