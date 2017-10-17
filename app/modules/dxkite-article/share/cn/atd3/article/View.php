<?php
namespace cn\atd3\article;

use bootstrap\Component;
use cn\atd3\article\dao\CategoryDAO;
use cn\atd3\user\dao\UserDAO;

class View
{
    public static function template($compiler){
        $compiler->addCommand('_cate2name','cn\atd3\article\View::cateid2name',true);
        $compiler->addCommand('_id2name','cn\atd3\article\View::userid2name',true);
    }
    public static function cateid2name($id){
        return (new CategoryDAO)->setFields(['name'])->getByPrimaryKey($id)['name']??__('默认分类');
    }
    public static function userid2name($id){
        return (new UserDAO)->setFields(['name'])->getByPrimaryKey($id)['name']??__('匿名');
    }
}
