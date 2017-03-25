<?php
namespace cn\atd3\response\user;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /user/signin as all method to run this class.
* you call use u('user_signin',Array) to create path.
* @template: default:user/signin.tpl.html
* @name: user_signin
* @url: /user/signin
* @param: 
*/
class Signin extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        return $this->display('lost_found:user/signin', ['title'=>'Welcome to use Suda!']);
    }
}
