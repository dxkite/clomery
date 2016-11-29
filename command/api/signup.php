<?php
namespace api;

class Signup extends Visitor
{
    public $auth=null;
    public $class=__CLASS__;

    function apiMain(Param $param){
        return ['hello'];
    }
}

