<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url / as all method to run this class.
* you call use u('index',Array) to create path.
* @template: default:index.tpl.html
* @name: index
* @url: /
* @param: 
*/
class Index extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        // params if had
        throw new \Exception('Some Exception');
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:index', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
