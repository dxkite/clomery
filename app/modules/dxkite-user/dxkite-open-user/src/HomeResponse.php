<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;


class HomeResponse extends UserSignResponse
{
    
    public function onUserVisit(Request $request)
    {
        $view = $this->view('home/index');
        $view->set('title', '个人中心');
        $view->set('user', $this->visitor->getAttributes());
        return $view;
    }
}
