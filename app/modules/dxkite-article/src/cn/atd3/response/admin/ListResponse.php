<?php
namespace cn\atd3\response\admin;

use suda\core\Session;
use suda\core\Cookie;
use suda\core\Request;
use suda\core\Query;

use cn\atd3\user\response\OnUserVisitorResponse;
use cn\atd3\visitor\Context;
use cn\atd3\article\dao\ArticleDAO;

// use cn\atd3\api\Proxy;
// use cn\atd3\article\proxyobject\ArticleManagerProxy;

class ListResponse extends OnUserVisitorResponse
{
    /**
     * 
     * @acl article_list
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $page=$this->page('dxkite/article:admin/list');
        // $article=new Proxy(new ArticleManagerProxy($context));
        $dao=new ArticleDAO;
        $request=$context->getRequest();
        $now=$request->get()->page(1);
        if ( isset($request->get()->status) && $request->get()->id(false)) {
            $dao->setStatus($request->get()->id, $request->get()->status);
            return $this->forward();
        }
        if($request->get()->search){
            $page->set('search',$request->get()->search);
            $list=$dao->setFields(['id','user','title','create','modify','category','type','views','status'])->order('modify',ArticleDAO::ORDER_DESC)->search(['title','content'],$request->get()->search,$now, 10)->fetchAll();
            $page->set('page.next',count($list)>=10);
        }else{
            $list=$dao->setFields(['id','user','title','create','modify','category','type','views','status'])->order('modify',ArticleDAO::ORDER_DESC)->list($now, 10);
            $count=$dao->getCount();
            $page->set('page.max', ceil($count/10));
        }
        $page->set('title',__('文章列表 第%d页',$now));
        $page->set('page.now', $now);
        $page->set('page.router', 'article:admin_list');
        $page->set('article',$list );
        return $page->render();
    }
}
