<?php
namespace cn\atd3\response\baidu;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;
use cn\atd3\oauth\baidu\Manager;
use cn\atd3\visitor\Context;

class CheckResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $manager=new Manager;
        $request=$context->getRequest();
        if ($code=$request->get()->code) {
            $result=$manager->baiduSign($context->getVisitor(),$code);
            if ($result === false) {
                $this->page('baidu/check-faild')->render();
            } elseif ($result===true) {
                $this->page('baidu/check-ok')->render();
            } else {
                $this->page('baidu/sign')->render();
            }   
        } else {
            $page=$this->page('baidu/check');
            $page->set('auth_url', Manager::getAuthUrl());
            return $page->render();
        }
    }
}
