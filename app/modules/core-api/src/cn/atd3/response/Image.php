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
* visit url /v1.0/verify_image as all method to run this class.
* you call use u('verify_image',Array) to create path.
* @template: default:v1/image.tpl.html
* @name: verify_image
* @url: /v1.0/verify_image
* @param: 
*/
class Image extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $this->type('png');
        $this->noCache();
        (new \cn\atd3\VerifyImage)->create();
    }
}
