<?php
namespace cn\atd3\response;

// use namespace
use suda\core\Request;
// database query
use suda\core\Query;
// site cookie
use suda\core\Cookie;
// site session
use suda\core\Session;

/**
* visit url /v1.0/crash[/{action}] as all method to run this class.
* you call use u('crash_api',Array) to create path.
* @template: default:v1/crash.tpl.html
* @name: crash_api
* @url: /v1.0/crash[/{action}]
* @param: action:string,
*/
class Crash extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        // params if had
        $action=$request->get()->action('action');
        // param values array
        $value=array('action'=>$request->get()->action('action'),);
        // display json code 
        return $this->json(['helloworld'=>'Hello,World!', 'value'=>$value]);
    }
}
