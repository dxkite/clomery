<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\{User};

/**
* visit url /admin/add as all method to run this class.
* you call use u('admin_add',Array) to create path.
* @template: default:admin/add.tpl.html
* @name: admin_add
* @url: /admin/add
* @param:
*/
class Add extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
         $this->set('site:selectid', 'admin_add');
        return $this->display('lost_found:admin/add', ['title'=>'Welcome to use Suda!', 'helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
