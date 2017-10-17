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

namespace cn\atd3\response\article\category;

use suda\core\{Session,Cookie,Request,Query};
use cn\atd3\visitor\Context;
use cn\atd3\article\view\Article;

class CategoryListResponse extends \cn\atd3\user\response\OnVisitorResponse
{
    public function onVisit(Context $context)
    {
        $request=$context->getRequest();
        $view=new Article($context);
        $category_id=$request->get()->category(0);
        $page_num=$request->get()->page(1);
        $info=$view->categoryInfo($category_id);
        $page=$this->page('dxkite/article:1.0.0:article/category_list');
        $page->set('title', 'Category - '.$info['name']);
        $page->set('list',$view->getList($page_num,10));
        $page->set('category',$info);
        return $page->render();
    }
}
