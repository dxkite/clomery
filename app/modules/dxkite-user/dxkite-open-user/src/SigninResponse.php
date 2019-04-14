<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;


class SigninResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $view = $this->view('signin');
        if ($request->hasPost('account')) {
            $account = $request->post('account');
            $password = $request->post('password');
            $code = $request->post('code');
            $remember = $request->post('remember', false);
            $up = new UserProvider;
            $up->loadFromContext($this->context);
            try {
                $session = $up->signin($account, $password, $code, $remember);
                $session->processor($this->application, $this->request, $this->response);
                $this->jumpForward();
                return;
            } catch (UserException $e) {
                if ($e->getCode() === UserException::ERR_CODE) {
                    $view->set('invaildCode', true);
                } else {
                    $view->set('invaildInput', true);
                }
            }
        }
        return $view;
    }
    
    public function onUserVisit(Request $request)
    {
        $this->jumpForward();
    }
}
