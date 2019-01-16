<?php
namespace dxkite\user\response\setting;

use dxkite\user\table\UserTable;
use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;
use dxkite\user\provider\AdminUserProvider;

class ListResponse extends \dxkite\support\setting\Response
{
    protected $provider;
    /**
     * 列出用户
     *
     * @acl user.list
     * @param [type] $view
     * @param [type] $context
     * @return void
     */
    public function onAdminView($view, $context)
    {
        $this->provider = new AdminUserProvider;
        $page = request()->get('page', 1);

        if (visitor()->hasPermission('user.status')) {
            if (request()->get('active', 0)>0) {
                $this->provider->modifyStatus([request()->get('active')], UserTable::STATUS_ACTIVE);
                return $this->refresh();
            }
            if (request()->get('freeze', 0)>0) {
                $this->provider->modifyStatus([request()->get('freeze')], UserTable::STATUS_FREEZE);
                return $this->refresh();
            }
        }

        if (visitor()->hasPermission('delete')) {
            if (request()->get('delete', 0)>0) {
                $this->provider->delete([request()->get('delete')]);
                return $this->refresh();
            }
        }
        
        if (request()->hasGet('search')) {
            if (request()->get('type') =='name') {
                $list= $this->provider->search('name', request()->get('search'));
            } elseif (request()->get('type')=='email') {
                $list= $this->provider->search('email', request()->get('search'));
            } else {
                $list= $this->provider->search('mobile', request()->get('search'));
            }
            $view->set('title', __('用户列表 - 搜索结果 第$0页', $page));
        } else {
            $list = $this->provider->list($page);
            $view->set('title', __('用户列表 第$0页', $page));
        }
 
        $pageBar = $list['page'];
        $pageBar['router'] = module(__FILE__).':admin_list';
        $view->set('list', $list);
        $view->set('page', $pageBar);
    }

    public function adminContent($template)
    {
        $template->include(module(__FILE__).':setting/list');
    }
}
