<?php
namespace cn\atd3\response\admin;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\{User,LostFound};

/**
* visit url /admin as all method to run this class.
* you call use u('admin_index',Array) to create path.
* @template: default:admin/index.tpl.html
* @name: admin_index
* @url: /admin
* @param: 
*/
class Index extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        $this->set('site:selectid','admin_site');
        return $this->display('lost_found:admin/index', ['title'=>'Welcome to use Suda!','select'=>5]);
    }

}
