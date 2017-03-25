<?php
namespace cn\atd3\response\test;

use suda\core\{Session,Cookie,Request,Query};

/**
* visit url /test as all method to run this class.
* you call use u('test',Array) to create path.
* @template: default:test/response.tpl.html
* @name: test
* @url: /test
* @param: 
*/
class Response extends \cn\atd3\BaseResponse
{
    public function onRequest(Request $request)
    {
        // params if had
        ;
        // param values array
        $value=array();
        // display template
        return $this->display('lost_found:test/response', ['title'=>'Welcome to use Suda!','helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
