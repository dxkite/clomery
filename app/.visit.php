<?php 
// SEO 优化 蜘蛛专用样式
if (is_spider())
{
    View::theme('spider');
}


Page::visit('/',['Main','main'])->use('index')->id('home_page');
Page::visit('/{id}?',['Main','article'])->with('id','int')->use('index')->id('home_page');
// 访问的url为 /QAQ ,无回调函数，使用404的页面，返回状态404，设置名称为 404_page
Page::visit('/QAQ',null)->use(404)->status(404)->id('404_page');

Page::default(function ($path) {
    View::set('title', '页面找不到了哦！');
    View::set('url', $path);
})->use(404)->status(404);
// TODO : 自动寻址需要优化
Page::auto('/@_@', ['/admin']);
