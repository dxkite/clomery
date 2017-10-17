<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\Pages;
use cn\atd3\user\response\OnVisitorResponse;
use cn\atd3\visitor\Context;

class PagesResponse extends OnVisitorResponse
{

    public function onVisit(Context $context)
    {
        return Pages::render(self::$name,$this);
    }
}
