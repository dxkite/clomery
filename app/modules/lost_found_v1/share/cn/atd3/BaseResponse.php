<?php
namespace cn\atd3;

use suda\core\Cookie;

/**
* nothing
*
*/

abstract class BaseResponse extends \suda\core\Response
{
    public function onPreTest($data)
    {
        Session::start();
        Cookie::set('client', 1)->session()->httpOnly();
        Cookie::set('token', 'c7b04d1534f1ed7bb9241cf5fe6ea11e')->session()->httpOnly();
        return true;
    }
}
