<?php 
// SEO 优化 蜘蛛专用样式
// if (is_spider()) {
//     View::theme('spider');
// }

if (conf('Uninstall')) {
    // 找不到页面时
    Page::default(['Install', 'start']);
} else {

// 主页
Page::visit('/', ['Main', 'main'])->use('index')->id('main_page')->noCache();
// 待开发的页面
Page::visit('/{pagename}', 'Develop->main')
->with('pagename', '/^(notes|question|test|books|about)$/')
->use('developing')->id('develop_page')->noCache();

    Page::visit('/article/{page}?', 'article\View->list')
->with('page', 'int')->id('article_list')->noCache();



    Page::visit('/article/category:{name}/{page}?', 'article\View->listCategory')
->with('name', 'string')->with('page', 'int')->id('article_category_list')->noCache();
    Page::visit('/article/tag:{name}/{page}?', 'article\View->listTag')
->with('name', 'string')->with('page', 'int')->id('article_tag_list')->noCache();
    Page::visit('/user:{userid}/{username}?', 'user\View->main')
->with('userid', 'int')->with('username', 'string')->id('user_view')->override()->noCache();
// 查看文章
    Page::visit('/article:{aid}/{name}?', 'article\View->article')
->with('aid', 'int')->with('name', 'string')->id('article_view')->override()->noCache();



    Page::visit('/mail-verify:{uid}.{token}', 'verify@user/email_verify')
    ->with('uid', 'int')
    ->with('token', 'string')->id('mail_verify')->noCache();


    Page::visit('/theme/{path}', function ($path_raw) {
        $type=pathinfo($path_raw, PATHINFO_EXTENSION);
        $path_raw=rtrim($path_raw, '/');
        if (Storage::exist(APP_VIEW.'/'.$path_raw)) {
            Page::getController()->raw()->type($type);
            echo Storage::get(APP_VIEW.'/'.$path_raw);
        } else {
            Page::error404($path_raw);
        }
    })->with('path', '/^(.+)$/')->id('theme')->override()->age(10000)->close();

    Page::visit('/upload:{id}/{name}?', ['Resource', 'main'])
->with('id', 'int')
->with('name', 'string')
->id('upload')->override();
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
