<?php
namespace admin;

use Page;
use Blog_Article as Article;
use Request;

class Article extends \Admin_Autoentrance
{
    public function run()
    {
        self::list();
    }

    public function list()
    {
        Page::set('title', '文章管理');
        $page_count=10;
        $page=Request::get()->page(1);
        $article=Article::listArticles($page, $page_count);
        $count=Article::count();
        $max=ceil($count/$page_count);
        $pages=range($page-5>1?$page-5:1, $page+5>$max?$max:$page+5);
        Page::set('pages', $pages);
        Page::set('page_current', $page);
        Page::set('article_list', $article);
        Page::insertCallback('Admin-Content', function () {
            Page::render('admin/article-list');
        });
    }
}
