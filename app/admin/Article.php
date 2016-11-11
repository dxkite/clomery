<?php
namespace admin;

use Page;
use Blog_Article as Article;
use Request;
use Core\Value;

class Article extends \Page_Admin
{
    public function run()
    {
        if ($aid=Request::get()->edit) {
            if (Request::hasPost()) {
                var_dump(Article::setArticle(Request::get()->edit, Request::post()->title, Request::post()->remark, Request::post()->contents));
            }
            self::edit($aid);
        } elseif ($aid=Request::get()->delete) {
            Article::delete($aid);
            self::list();
        }elseif ($aid=Request::get()->private) {
            Article::publish($aid, 0);
            self::list();
        } elseif ($aid=Request::get()->public) {
            Article::publish($aid, 1);
            self::list();
        } elseif ($aid=Request::get()->verified) {
            Article::verify($aid, 0);
            self::list();
        } elseif ($aid=Request::get()->verify) {
            Article::verify($aid, 1);
            self::list();
        } else {
            if (Request::hasPost()) {
                var_dump($article=Request::post()->artilce);
                foreach ($article as $aid=>$on) {
                    if ($on=='on') {
                        Article::delete($aid);
                    }
                }
                header('Location:'.$_SERVER['PHP_SELF']);
                return;
            }
            self::list();
        }
    }
    
    public function edit(int $aid)
    {
        Page::set('title', '文章编辑');
        $article=Article::getArticle($aid);
        Page::set('article', new Value($article));
        Page::insertCallback('Admin-Content', function () {
            Page::render('admin/article-edit');
        });
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
