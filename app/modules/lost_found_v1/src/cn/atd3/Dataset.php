<?php
namespace cn\atd3;
use suda\core\Response;
/**
* nothing
* 
*/

class Dataset 
{
    function sidebanner(){
        $check=LostFound::countIfCheck(0);
        $barlist=[
           'admin_site'=>['url'=>u('admin_index'),'name'=>'网站管理'],
            ['url'=>'#','name'=>'物品管理','child'=>[
                'admin_add'=>['url'=>u('admin_add'),'name'=>'添加物品'],
                'admin_check'=>['url'=>u('admin_check'),'name'=>'待审核物品'],
                'admin_list'=>['url'=>u('admin_list'),'name'=>'全部物品']
            ]]
        ];
        Response::set('site:barlist',$barlist);
    }
}
