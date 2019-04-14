<?php
namespace dxkite\openuser\setting\response\client;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use dxkite\openuser\setting\provider\ClienProvider;

class EditResponse extends SettingResponse
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
        $view = $this->view('client/edit');

        $id = $request->get('id', 1);
        $site = $provider->get($id);

        if ($request->hasPost('name') ) {
            $name = $request->post('name');
            $description = $request->post('description');
            $hostname = $request->post('hostname');
            $hostname = empty($hostname)?null:$hostname;
            if ($provider->edit($id, $name, $description, $hostname)) {
                $this->goRoute('client_list');
            }else{
                $view->set('failed', true);
                $view->set('site', $site);
            }
        }
        
        $view->set('site', $site);
        $view->set('title', $this->_('网站 $0 详情', $site['name']));
        $view->set('submenu', $this->_('$0 详情', $site['name']));
        return $view;
    }
}
