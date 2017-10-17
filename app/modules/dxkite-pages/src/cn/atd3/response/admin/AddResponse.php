<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\Pages;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use suda\template\compiler\suda\TemplateInfo;

class AddResponse extends OnUserVisitorResponse
{
    /**
     * 
     * @acl add_page
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('pages:admin/add');
        $new=Pages::getNew();
        $page->set('page',$new);
        return $page->render();
    }


    public function getTemplateOptions(int $id)
    {
        $templates=TemplateInfo::getTemplates();
        $select=Pages::getTemplate($id);
        if (empty($select)) {
            $list[] = '<option selected="selected">' . __('请选择模板') . '</option>';
        }
        foreach ($templates as $module=>$items) {
            $list[]= '<optgroup label="'. $module .'">';
            foreach ($items as $name=>$path) {
                $fullname=$module.':'.$name;
                $show= $fullname==$select?' selected="selected" ':'';
                $list[]='<option value="'.$fullname.'" '.$show.' title="'.$path.'">'.$fullname. '</option>';
            }
            $list[] = '</optgroup>';
        }
        return join("\n", $list);
    }
}
