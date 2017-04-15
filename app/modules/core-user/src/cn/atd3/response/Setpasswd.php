<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /set-passsword/{id:int}/{token} as all method to run this class.
* you call use u('setpasswd',Array) to create path.
* @template: default:setpasswd.tpl.html
* @name: setpasswd
* @url: /set-passsword/{id:int}/{token}
* @param: id:int,token:string,
*/
class Setpasswd extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        // params if had
        $id=$request->get()->id(0);
		$token=$request->get()->token('token');
        // param values array
        $value=array('id'=>$request->get()->id(0),'token'=>$request->get()->token('token'),);
        // display template
        return $this->display('user:setpasswd', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
