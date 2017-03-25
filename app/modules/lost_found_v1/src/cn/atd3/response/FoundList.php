<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /founds as all method to run this class.
* you call use u('found_list',Array) to create path.
* @template: default:found_list.tpl.html
* @name: found_list
* @url: /founds
* @param: 
*/
class FoundList extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:found_list', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
