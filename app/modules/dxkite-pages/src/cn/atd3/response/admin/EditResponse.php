<?php
namespace cn\atd3\response\admin;

use cn\atd3\dao\PagesDAO;
use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\Pages;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use suda\template\compiler\suda\TemplateInfo;

class EditResponse extends OnUserVisitorResponse
{
    /**
     * 
     * @acl edit_page
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('pages:admin/edit');
        $id=$request->get()->id(0);
        $dao=new PagesDAO;
 
        if ($item=$dao->getById($id)) {
            $page->set('page', $item);
        } else {
            $page->set('invaild_id', true);
        }
        return $page->render();
    }

    public function getTemplateValues(string $name)
    {
        $values=Pages::getTemplateInfo($name)->getValuesName();
        foreach ($values as $name) {
            $list[]='<option value="'.$name.'">'.$name. '</option>';
        }
        return join("\n", $list);
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
