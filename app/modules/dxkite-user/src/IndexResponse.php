<?php
namespace dxkite\user\response;

use dxkite\support\visitor\response\VisitorResponse;
use dxkite\support\visitor\Context;
use dxkite\user\controller\UserController;

class IndexResponse extends VisitorResponse
{
    public function onGuestVisit(Context $context)
    {
        if ($userId=request()->get('id')) {
            $this->info($userId);
        } else {
            $this->go(u('signin'));
            cookie()->set('redirect_uri', u($_GET));
            echo __('正在跳转登陆');
        }
    }
    
    public function onUserVisit(Context $context)
    {
        if ($userId=request()->get('id')) {
            $this->info($userId);
        } else {
            $this->home();
        }
    }
    
    protected function info(int $userId)
    {
        $view=$this->page('index');
        if ($user=(new UserController)->get($userId)) {
            $view->set('user', $user);
        }
        $view->render();
    }

    protected function home()
    {
        $view=$this->page('home/index');
        if ($user=(new UserController)->get(get_user_id())) {
            $view->set('user', $user);
        }
        $view->render();
    }
}
