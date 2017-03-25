<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\User;
use cn\atd3\LostFound;

/**
* visit url /admin/check as all method to run this class.
* you call use u('admin_check',Array) to create path.
* @template: default:admin/check.tpl.html
* @name: admin_check
* @url: /admin/check
* @param:
*/
class Check extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        $founds=LostFound::listByCheck(0);
        $this->set('site:selectid','admin_check');
        return $this->display('lost_found:admin/check', ['founds'=>  $founds,'title'=>'物品审核', 'select'=>5]);
    }
}
