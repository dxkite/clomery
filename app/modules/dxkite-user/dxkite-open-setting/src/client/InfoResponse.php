<?php
namespace dxkite\openuser\setting\response\client;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use dxkite\openuser\setting\provider\ClienProvider;

class InfoResponse extends SettingResponse
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
        $view = $this->view('client/info');

        $id = $request->get('id', 1);

        if ($this->visitor->hasPermission('open-client.token')) {
            if ($request->hasGet('refresh')) {
                $provider->reset($id);
                $this->goThisWithout(['refresh']);
                return;
            }
        }

        $site = $provider->get($id);
        $view->set('site', $site);
        $view->set('title', $this->_('网站 $0 详情', $site['name']));
        $view->set('submenu', $this->_('$0 详情', $site['name']));
        return $view;
    }
}
