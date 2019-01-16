<?php
namespace dxkite\user\response;

use dxkite\support\visitor\response\VisitorResponse;
use dxkite\support\visitor\Context;

abstract class JumpBaseResponse extends VisitorResponse
{
    public function onGuestVisit(Context $context)
    {
    }

    public function onUserVisit(Context $context)
    {
        self::_jumpForward();
    }
    
    public function _jumpForward()
    {
        $this->jump($this->getJumpUrl());
    }

    public function getJumpUrl()
    {
        if ($refer = $this->getForward()) {
            $mapping=router()->parseUrl($refer);
            if ($mapping && request()->getMapping()->is($mapping)) {
                return u('index');
            } else {
                return $refer;
            }
        } else {
            return u('index');
        }
    }

    public function jump(string $url)
    {
        $this->go($url);
        echo __('正在跳转');
        return;
    }
}
