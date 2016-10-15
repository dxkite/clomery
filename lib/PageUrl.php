<?php

class PageUrl
{
    public static function articlePage(int $page=0)
    {
        if ($page) {
            return Page::url('article_list', ['page'=>$page]);
        }
        return Page::url('article_list');
    }
    public static function article(int $aid, string $name)
    {
        return Page::url('article_view', ['aid'=>$aid, 'name'=>$name]);
    }
}
