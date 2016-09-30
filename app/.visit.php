<?php 
// SEO 优化 蜘蛛专用样式
if (is_spider())
{
    View::theme('spider');
}
// 主页
Page::visit('/',['Main','main'])->use('index')->id('main_page');

// 查看文章
Page::visit('/article/{id}?',['Main','article'])->with('id','int')->use('index')->id('main_article');

// 404 页面 访问的url为 /QAQ ,无回调函数，使用404的页面，返回状态404，设置名称为 404_page
Page::visit('/QAQ',null)->use(404)->status(404)->id('404_page');

// 留言板 
Page::visit('/notes',['Notes','main'])->id('notes_page');

Page::visit('/resource/{path}',function ($path_raw) {
    $type=pathinfo($path_raw,PATHINFO_EXTENSION);
    $path_raw=rtrim($path_raw,'/');
    if (Storage::exist(APP_VIEW.'/'.$path_raw))
    {
        Page::getController()->raw()->type($type);
        echo Storage::get(APP_VIEW.'/'.$path_raw);
    }
    else
    {
        View::set('title', '找不到相关资源！');
        View::set('url', $path_raw);
        Page::controller()->use(404)->status(404);
    }
})->with('path','/^(.+)$/')->id('resource')->override();

// 管理界面导向
Page::auto('/@_@', '/admin')->id('admin');

// 找不到页面时
Page::default(function ($path) {
    View::set('title', '页面找不到了哦！');
    View::set('url', $path);
})->use(404)->status(404);