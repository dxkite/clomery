<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\UserDAO;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class AddResponse extends OnUserVisitorResponse
{
    /**
     * 添加用户
     * @acl add_user
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('user:admin/add');
        $page->set('name', $request->post()->name(''));
        $page->set('email', $request->post()->email(''));
        $page->set('passwd', $request->post()->password(''));
        if ($request->hasPost()) {
            $info=(new UserDAO)->add($request->post()->name, $request->post()->email, $request->post()->password, $request->post()->group_id);
            switch ($info) {
                case UserDAO::EXISTS_USER:
                    $page->set('invaild_name', true);
                    break;
                case UserDAO::EXISTS_EMAIL:
                    $page->set('invaild_email', true);
                    break;
                default:
                    $page->set('success', true);
            }
        }
        $page->set('admin.sidebar', [
            [
                'sort'=>2,
                'id'=>'user:admin_add',
                'text'=>__('添加用户'),
                'href'=>u('user:admin_add'),
            ]
        ]);

        $page->set('title', __('添加用户'));
        $page->set('groups', (new GroupDAO)->list());
        return $page->render();
    }
}
