<?php
namespace cn\atd3\response;

use suda\core\{Session,Cookie,Request,Query};

class AvatarResponse extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $this->go(assets('user','img/avatar.png'));
    }
}
