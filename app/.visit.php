<?php 
// SEO 优化 蜘蛛专用样式
if (is_spider()) {
    View::theme('spider');
}
if (conf('Uninstall')) {
    // 找不到页面时
    Page::default(['Install','start']);
} else {

// 主页
Page::visit('/', ['Main', 'main'])->use('index')->id('main_page')->noCache();
// 待开发的页面
Page::visit('/{pagename}', ['Develop', 'main'])
->with('pagename', '/^(notes|question|test|books|about)$/')
->use('developing')->id('develop_page')->noCache();

    Page::visit('/article/{page}?', ['article\View', 'list'])
->with('page', 'int')->id('article_list')->noCache();

    Page::visit('/u/{userid}/{username}?', ['UserView', 'main'])
->with('userid', 'int')->with('username', 'string')->id('user_view')->override()->noCache();

    Page::visit('/article-{aid}/{name}?', ['article\View', 'article'])
->with('aid', 'int')->with('name', 'string')->id('article_view')->override()->noCache();
// 查看文章
//Page::visit('/article/{id}?',['Main','article'])->with('id','int')->use('index')->id('main_article');



// 留言板
//Page::visit('/notes',['Notes','main'])->id('notes_page');

Page::visit('/!{path}', function ($path_raw) {
    $type=pathinfo($path_raw, PATHINFO_EXTENSION);
    $path_raw=rtrim($path_raw, '/');
    if (Storage::exist(APP_VIEW.'/'.$path_raw)) {
        Page::getController()->raw()->type($type);
        echo Storage::get(APP_VIEW.'/'.$path_raw);
    } else {
        Page::error404($path_raw);
    }
})->with('path', '/^(.+)$/')->id('resource')->override();

    Page::visit('/${id}/{name}?', ['Resource', 'main'])
->with('id', 'int')
->with('name', 'string')
->id('upload_file')->override();

    Page::auto('/user', '/user')->id('user');
    // 管理界面导向
    Page::auto('/0.0', '/admin')->id('admin');
    Page::auto('/test', '/test')->id('test');
    // 验证码
    Page::visit('/verify_code', function () {
        (new Image())->verifyImage();
    })->raw()
    ->type('png')
    ->id('verify_code');
    // 找不到页面时
Page::default(function ($path) {
        Page::error404($path);
    })->use(404)->status(404);
// 404 页面 访问的url为 /QAQ ,无回调函数，使用404的页面，返回状态404，设置名称为 404_page
Page::visit('/QAQ', function () {
    Page::error404();
})->use(404)->status(404)->id('404_page');
}
