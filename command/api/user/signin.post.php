<?php
namespace api\user;

use api\Visitor;
use api\Param;
use Three;

class Signin extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
        return Three::request()->post();
    }
}
