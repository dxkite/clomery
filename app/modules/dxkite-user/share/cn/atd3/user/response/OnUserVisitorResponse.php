<?php
namespace cn\atd3\user\response;

use cn\atd3\visitor\Context;

abstract class OnUserVisitorResponse extends OnVisitorResponse
{
    public function onGuestVisit(Context $context)
    {
        return $this->go(u('user:signin', ['from'=>u()]));
    }
}
