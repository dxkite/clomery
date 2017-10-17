<?php
namespace cn\atd3\response;

use cn\atd3\visitor\Context;
use cn\atd3\setting\template\Manager;
use cn\atd3\dao\SettingDao;

class TemplateResponse extends \cn\atd3\user\response\OnUserVisitorResponse
{
    public function onUserVisit(Context $context)
    {
        $page=$this->page('template');
        $request=$context->getRequest();
        if ($request->get()->template) {
            (new SettingDao)->set('template', $request->get()->template('default'));
            $this->forward();
        }
        elseif ($request->get()->delete) {
            Manager::instance()->delete($request->get()->delete);
            $this->forward();
        }
        $list=Manager::instance()->getTemplateShowList();
        $page->set('list', $list);
        $page->set('modules', json_encode(app()->getModules()));
        $page->render();
    }
}
