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
    public static function article(int $aid, string $name='')
    {
        return Page::url('article_view', ['aid'=>$aid, 'name'=>$name]);
    }
    public static function avatar(int $uid)
    {
        $info=Common_User::getPublicInfo($uid);
        return Page::url('upload', ['id'=> $info['avatar'], 'name'=> $info['name']]);
    }
    public static function UserHome(int $uid)
    {
        return Page::url('user_view', ['userid'=> $uid]);
    }

    public static function categoryPage(string $name, int $page=0)
    {
        if ($page) {
            return Page::url('article_category_list', ['name'=>$name, 'page'=>$page]);
        }
        return Page::url('article_category_list', ['name'=>$name]);
    }
    public static function tagPage(string $name, int $page=0)
    {
        if ($page) {
            return Page::url('article_tag_list', ['name'=>$name, 'page'=>$page]);
        }
        return Page::url('article_tag_list', ['name'=>$name]);
    }
    public static function theme(string $file)
    {
        return Page::url('theme',['path'=>$file]);
    }
}
