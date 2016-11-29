<?php
namespace api\user;

use api\Visitor;
use api\Param;
use Kite;

class Signin extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    public function apiMain(Param $param)
    {
        return Kite::request()->post();
    }
}
