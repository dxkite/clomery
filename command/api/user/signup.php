<?php
namespace api\user;

use api\Visitor;
use api\Param;

class Signup extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
        return ['hello'];
    }
}
