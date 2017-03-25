<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /losts as all method to run this class.
* you call use u('lost_list',Array) to create path.
* @template: default:lost_list.tpl.html
* @name: lost_list
* @url: /losts
* @param: 
*/
class LostList extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:lost_list', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
