<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\User;

/**
* visit url /profile as all method to run this class.
* you call use u('profile',Array) to create path.
* @template: default:profile.tpl.html
* @name: profile
* @url: /profile
* @param: 
*/
class Profile extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $id=User::getUserId();
        // 未登录
        if (!$id){
            return $this->redirect(u('user:signin'));
        }

        return $this->display('user:profile', ['user_id'=>$id]);
    }
}
