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
    public static function Avatar(int $uid)
    {
        $info=UManager::getPublicInfo($uid);
        return Page::url('upload_file', ['id'=> $info['avatar'], 'name'=> $info['name']]);
    }
    public static function UserHome(int $uid)
    {
        return Page::url('user_view', ['userid'=> $uid]);
    }
}
