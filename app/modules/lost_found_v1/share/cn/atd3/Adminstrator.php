<?php
namespace cn\atd3;

/**
* nothing
*
*/

abstract class Adminstrator extends BaseResponse
{
    public function onPreTest($data)
    {
        parent::onPreTest($data);
        $user=User::getInstance();
        if (!$user->hasSignin()) {
            header('Location:'.u('user_signin'));
            return true;
        } elseif (!$user->checkPermission('admin')) {
            return false;
        }
        return true;
    }
}
