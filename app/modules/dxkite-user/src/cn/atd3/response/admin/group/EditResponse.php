<?php
/**
 * Suda FrameWork
 *
 * An open source application development framework for PHP 7.0.0 or newer
 * 
 * Copyright (c)  2017 DXkite
 *
 * @category   PHP FrameWork
 * @package    Suda
 * @copyright  Copyright (c) DXkite
 * @license    MIT
 * @link       https://github.com/DXkite/suda
 * @version    since 1.2.4
 */

namespace cn\atd3\response\admin\group;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\user\dao\GroupDAO;
use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;

class EditResponse extends OnUserVisitorResponse
{
    /**
     * 添加分组
     * @acl edit_group
     * @param int $uid
     * @param Request $request
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $request=$context->getRequest();
        $page=$this->page('dxkite/user:admin/group/edit');
        $dao=new GroupDAO;
        $all=$dao->getAuths();
        $id=$request->get()->id;
        if ($request->hasPost()) {
            $dao->setPermission($id, array_keys($request->post()->auths([])));
        }
        $info=$dao->getById($id);
        if(!$info){
            $page->set('invaild_id',true);
        }
        $page->set('name', $info['name']);
        $page->set('title',__('编辑分组_%s', $info['name']));
        $page->set('permissions', $info['permissions']);
        $page->set('auths', $all);
        return $page->render();
    }

    private function treeList(array $list){

    }
}
