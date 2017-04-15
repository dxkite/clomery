<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\User;

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
        if (User::getUserId()){
            return $this->redirect(u('user:profile'));
        }
        if ($request->get()->redirect){
            $this->set('redirect',$request->get()->redirect);
        }
        return $this->display('user:signin');
    }
}
