<?php
namespace cn\atd3\response\admin;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\{User,LostFound};
/**
* visit url /admin/list as all method to run this class.
* you call use u('admin_list',Array) to create path.
* @template: default:admin/list_all.tpl.html
* @name: admin_list
* @url: /admin/list
* @param: 
*/
class ListAll extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        $founds=LostFound::list();
        $this->set('site:selectid','admin_list');
        return $this->display('lost_found:admin/list_all',  ['founds'=>  $founds, 'title'=>'全部物品', 'select'=>5]);
    }
}
