<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /add as all method to run this class.
* you call use u('add_item',Array) to create path.
* @template: default:add_item.tpl.html
* @name: add_item
* @url: /add
* @param: 
*/
class AddItem extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:add_item', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
