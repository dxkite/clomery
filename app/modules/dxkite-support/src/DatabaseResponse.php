<?php
namespace dxkite\support\response;

use suda\core\Query;
use dxkite\support\visitor\Context;
use dxkite\support\database\DbHelper;
use dxkite\support\provider\DatabaseProvider;

class DatabaseResponse extends \dxkite\support\setting\Response
{
    protected $databaseProvider;
    /**
     * 备份数据库
     * 
     * @acl website.backup
     * 
     * @param Context $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->databaseProvider = new DatabaseProvider;
        if ($delete=request()->get('delete')) {
            $this->databaseProvider->delete($delete);
            $this->forward();
            return;
        }
        $list=$this->databaseProvider->list();
        if ($list) {
            $view->set('title', __('备份管理'));
            $view->set('list', $list);
        }
    }

    public function adminContent($template)
    {
        $template->include('support:database');
    }
}
