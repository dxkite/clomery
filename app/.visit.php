<?php 
// SEO 优化 蜘蛛专用样式
if (is_spider())
{
    View::theme('spider');
}


Page::visit('/',['Main','main'])->use('index');

Page::visit('/QAQ',function () {})->use(404)->status(404)->name('404_page');

Page::default(function ($path) {
    View::set('title', '页面找不到了哦！');
    View::set('url', $path);
})->use(404)->status(404);
Page::auto('/admin', ['/admin']);
