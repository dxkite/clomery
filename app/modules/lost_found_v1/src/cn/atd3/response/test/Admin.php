<?php
namespace cn\atd3\response\test;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /admintest as all method to run this class.
* you call use u('test',Array) to create path.
* @template: default:test/admin.tpl.html
* @name: test
* @url: /admintest
* @param: 
*/
class Admin extends \cn\atd3\Adminstrator
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:test/admin', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
