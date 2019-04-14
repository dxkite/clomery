<?php
namespace dxkite\openuser\setting\response\client;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use dxkite\openuser\setting\provider\ClienProvider;

class AddResponse extends SettingResponse
{
    /**
     * 管理员列表
     *
     * @acl open-client.add
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $provider = new ClienProvider;
        $provider->loadFromContext($this->context);
        $view = $this->view('client/add');
        if ($request->hasPost('name') ) {
            $name = $request->post('name');
            $description = $request->post('description');
            $hostname = $request->post('hostname');
            $hostname = empty($hostname)?null:$hostname;
            if ($provider->add($name, $description, $hostname)) {
                $this->goRoute('client_list');
            }else{
                $view->set('failed', true);
                $view->set('site', [
                    'name' => $name,
                    'description' => $description,
                    'hostname' => $hostname,
                ]);
            }
        }
        $view->set('title', '添加网站');
        $view->set('submenu',  '添加网站');
        return $view;
    }
}
