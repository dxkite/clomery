<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\setting\VerifyImage;
use support\setting\response\Response;

class VerifyImageResponse extends Response
{
    public function onGuestVisit(Request $request)
    {
        $this->generateImage();
    }
    
    public function onAccessVisit(Request $request)
    {
        $this->generateImage();
    }

    public function generateImage()
    {
        $verify = new VerifyImage($this->context, 'support/setting');
        \ob_start();
        $verify->display();
        $content = \ob_get_clean();
        $response = $this->context->getResponse();
        $response->setType('jpeg');
        $response->send($content);
    }
}
