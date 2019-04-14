<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\VerifyImage;
use dxkite\openuser\response\UserResponse;

class VerifyImageResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $this->generateImage();
    }
    
    public function onUserVisit(Request $request)
    {
        $this->generateImage();
    }

    public function generateImage()
    {
        $verify = new VerifyImage($this->context, 'dxkite/openuser');
        \ob_start();
        $verify->display();
        $content = \ob_get_clean();
        $response = $this->context->getResponse();
        $response->setType('jpeg');
        $response->send($content);
    }
}
