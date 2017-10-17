<?php
namespace cn\atd3\response\admin;

use cn\atd3\Pages;
use cn\atd3\dao\PagesDAO;
use suda\template\Manager;

class AjaxResponse extends \cn\atd3\api\response\OnCallableResponse
{
    /**
     * 获取模板值
     *  
     * @param string $name
     * @return void
     */
    public function getTemplateValues(string $name)
    {
        return Pages::getTemplateInfo($name)->getValuesName();
    }

    /**
     * @cal edit_page
     */
    public function setPageValue(int $id, string $name, string $value)
    {
        return (new PagesDAO)->setValue($id, $name, $value);
    }

    /**
     * @cal edit_page
     */
    public function getPageValue(int $id, string $value)
    {
        return (new PagesDAO)->getValue($id, $value);
    }

    /**
     * @cal edit_page
     *
     * @param int $id
     * @return void
     */
    public function save(int $id)
    {
        return (new PagesDAO)->setStatus($id);
    }


    /**
     * @acl gen_html
     */
    public function saveHtml(int $id)
    {
        return Pages::saveHtml($id,$this);
    }
    
    /**
     * @cal edit_page
     *
     * @param int $id
     * @param array $value
     * @return void
     */
    public function update(int $id, array $value)
    {
        return Pages::update($id, $value);
    }
}
