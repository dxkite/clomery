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
* visit url /v1.0 as all method to run this class.
* you call use u('main_api',Array) to create path.
* @template: default:v1/index.tpl.html
* @name: main_api
* @url: /v1.0
* @param:
*/
class Index extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        return $this->json(['apis'=>[]]);
    }
}
