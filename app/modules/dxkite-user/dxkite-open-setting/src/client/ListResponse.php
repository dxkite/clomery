<?php
namespace dxkite\openuser\setting\response\client;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use dxkite\openuser\setting\provider\ClienProvider;

class ListResponse extends SettingResponse
{
    /**
     * 管理员列表
     *
     * @acl open-client.list
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $provider = new ClienProvider;
        $provider->loadFromContext($this->context);
        $view = $this->view('client/list');
        $page = $request->get('page', 1);

        if ($this->visitor->hasPermission('open-client.delete')) {
            if ($request->get('delete', 0) > 0) {
                $provider->delete($request->get('delete'));
                $this->goThisWithout(['delete']);
                return;
            }
        }
        if ($this->visitor->hasPermission('open-client.token')) {
            if ($request->get('refresh', 0) > 0) {
                $provider->reset($request->get('refresh'));
                $this->goThisWithout(['refresh']);
                return;
            }
        }
        $list = $provider->list($page, 10);
        $pageBar = $list->getPage();
        $pageBar['router'] = 'user_list';
        $view->set('title', $this->_('网站列表 第$0页', $list->getPageCurrent()));
        $view->set('list', $list->getRows());
        $view->set('page', $pageBar);
        return $view;
    }
}
