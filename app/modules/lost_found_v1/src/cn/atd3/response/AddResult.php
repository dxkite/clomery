<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\{User,LostFound};

/**
* visit url /add/result as all method to run this class.
* you call use u('add_result',Array) to create path.
* @template: default:add_result.tpl.html
* @name: add_result
* @url: /add/result
* @param: 
*/
class AddResult extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        $post=$request->post();
        $user=User::getInstance();
         
        $id=LostFound::add($post->name,$post->where,$post->info,\cn\atd3\User::getUserId(),$post->qq,$post->phone,time(),intval($post->type(1)),$user->checkPermission('admin'),0);
        if ($id>0){
            $info='发布成功';
        }else{
            $info='发布失败';
        }
        return $this->display('lost_found:add_result', ['info'=>$info,'select'=>4]);
    }
}
