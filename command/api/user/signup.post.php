<?php
namespace api\user;

use api\Visitor;
use api\Param;
use model\User;
use Request;
use Kite;

class Signup extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
        return $param;
    }
}
