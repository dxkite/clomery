<?php
namespace api\user;

use api\Visitor;
use api\Param;
use model\User;
use Request;
use Three;

class Signup extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
         return api_check_values($param,['user','email','password','client_id','client_token'],'model\User::signUp');
    }
}
