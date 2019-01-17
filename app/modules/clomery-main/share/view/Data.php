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
            '首页' => u('index'),
            'Suda框架' => 'https://github.com/dxkite/suda',
        ]);
    }
}
