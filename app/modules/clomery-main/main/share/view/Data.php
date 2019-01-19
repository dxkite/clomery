<?php
namespace dxkite\clomery\main\view;

class Data
{
    public static function socialLinks($template)
    {
        $template->set('socialLinks', [
            'Github' => 'https://github.com/dxkite'
        ]);
    }
    public static function menu($template)
    {
        $template->set('menu', [
            __('首页') => u('article:index'),
            __('标签') => u('article:tags'),
            __('分类') => u('article:categorys'),
            __('归档') => u('main:archives'),
            'Suda框架' => 'https://github.com/dxkite/suda',
        ]);
    }

    public static function profile($template)
    {
        $template->set('profile', [
            'author' => 'dxkite',
            'avatar' => assets_url('article','images/dxkite.png'),
            'description' => 'Hello! I am DXkite'
        ]);
    }
}
