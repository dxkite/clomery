<?php
namespace dxkite\category\response;

use suda\core\route\Mapping;
use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;

abstract class BaseResponse extends \dxkite\support\setting\Response
{
    protected $target;
    protected $acl;
    protected $template;

    public function __construct($table)
    {
        $this->target = $table;
        $param = request()->getMapping()->getParam();
        $this->acl = $param['acl'] ?? [];
        $this->template = $param['template'] ?? null;
        $mapping=\suda\core\route\Mapping::current();
    }

    public function adminContent($template)
    {
        if ($this->template) {
            $include = $this->view($this->template);
        } else {
            $include = null;
        }
        if ($include) {
            $template->parent($include)->render();
        } else {
            $template->include(module(__FILE__).':'. $this->getTemplateName());
        }
    }

    public function onAdminView($view, $context)
    {
        if ($context->getVisitor()->hasPermission($this->acl)) {
            $view->set('module', app()->getActiveModule());
            return $this->contentAction($view, $context);
        } else {
            $this->onDeny($context);
            return false;
        }
    }

    abstract public function contentAction($view);
    abstract public function getTemplateName();
}
