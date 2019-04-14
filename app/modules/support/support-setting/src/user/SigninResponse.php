<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\setting\response\Response;
use support\setting\provider\UserProvider;
use support\setting\exception\UserException;
use support\setting\provider\VisitorProvider;

class SigninResponse extends Response
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
                $last = $this->history->last($this->context->getSession()->id(), 0 , $this->getUrl('index'));
                $this->redirect($last);
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
    
    public function onAccessVisit(Request $request)
    {
        $this->goRoute('index');
    }
}
