<?php
namespace cn\atd3\response\article\tag;

use suda\core\{Session,Cookie,Request,Query};

class TagListResponse extends \suda\core\Response
{
    public function onRequest(Request $request)
    {
        $page=$this->page('dxkite/article:1.0.0:article/tag_list');

        // params if had
        $tag=$request->get()->tag('tag');
        // param values array
        $value=array('tag'=>$request->get()->tag('tag'),);
        // display template

        $page->set('title', 'Welcome to use Suda!')
        ->set('helloworld', 'Hello,World!')
        ->set('value', $value);

        return $page->render();
    }
}
