<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /signin as all method to run this class.
* you call use u('signin',Array) to create path.
* @template: default:signin.tpl.html
* @name: signin
* @url: /signin
* @param: 
*/
class Signin extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('user:signin', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
