<?php 
// SEO 优化 蜘蛛专用样式
if (is_spider())
{
    View::theme('spider');
}
// 主页
Page::visit('/',['Main','main'])->use('index')->id('main_page');
// 待开发的页面
Page::visit('/{pagename}',['Develop','main'])
->with('pagename','/^(notes|question|test|books|article|about)$/')
->use('developing')->id('develop_page');
// 查看文章
//Page::visit('/article/{id}?',['Main','article'])->with('id','int')->use('index')->id('main_article');



// 留言板 
//Page::visit('/notes',['Notes','main'])->id('notes_page');

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
       Page::redirect(Page::url('404_page').'?url='.urlencode($path));
    }
})->with('path','/^(.+)$/')->id('resource')->override();


Page::auto('/user', '/user')->id('user');
// 管理界面导向
Page::auto('/@_@', '/admin')->id('admin');
// 验证码
Page::visit('/QvQ',function(){
    (new Image())->verifyImage();
})->raw()
->type('png')
->id('verify_code');
// 找不到页面时
Page::default(function ($path) {
    Page::redirect(Page::url('404_page').'?url='.urlencode($path));
})->use(404)->status(404);
// 404 页面 访问的url为 /QAQ ,无回调函数，使用404的页面，返回状态404，设置名称为 404_page
Page::visit('/QAQ',function(){
    import('Site.functions');
    Site\page_common_set();
    Page::set('url',urldecode($_GET['url']));
    Page::set('site_title', '页面找不到了哦！');
})->use(404)->status(404)->id('404_page');