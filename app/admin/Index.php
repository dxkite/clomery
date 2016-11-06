<?php
namespace admin;

use Page;
use Core\Caller;
use Core\Value;
use Request;
use Common_Navigation;

class Index
{
    public function entrance(string $addon)
    {
        if (\System::user()->hasSignin) {
            if (\System::user()->permission->editSite) {
                self::setBasic();
                self::loadContent($addon);
            } else {
                echo 'no perimission';
                Page::redirect('/');
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
    
    public function main()
    {
        self::entrance('main');
    }
    public function loadContent(string $name)
    {
        if (method_exists($this, 'content'.ucfirst($name))) {
            $this->{'content'.$name}();
        }
    }

    public function setBasic()
    {
        $options[]=new Value(['title'=>'网站信息', 'href'=>Page::url('admin')]);
        $options[]=new Value(['title'=>'网站设置', 'href'=>Page::url('admin', ['path'=>'site'])]);
        $options[]=new Value(['title'=>'导航栏设置', 'href'=>Page::url('admin', ['path'=>'navigation'])]);
        Page::set('options', $options);
        Page::use('admin/index');
    }

    public function contentMain()
    {
        $infos=[
                    'debug_mod'=>conf('DEBUG')?'true':'false',
                    'core_ver'=>CORE_VERSION,
                    'php_ver'=>PHP_VERSION,
                    'img_ver'=>conf('Driver.Image').'/'.\Image::version(),
                    
                    'db_ver'=>\Database::version(),
                    'user_num'=>\Common_User::numbers(),
                    'article_num'=>\Blog_Article::numbers(),
                    'env'=>$_SERVER['SERVER_SOFTWARE'],
                    
                    'upload_max'=>ini_get('upload_max_filesize'),
                    'safe_mode'=>ini_get('safe_mode')?'true':'false',
                ];

        Page::insertCallback('Admin-Content', function () {
            Page::render('admin/main');
        });
                
        Page::assign($infos);
    }
    public function contentNavigation()
    {
        $mod=Request::get()->mod;
        Page::set('id', Request::get()->id);
        Page::set('mod', $mod);
        switch ($mod) {
            case 'create':
            if (Request::post()->nav_create) {
                 Common_Navigation::create(Request::post()->nav_create);
                 header('Location:'.$_SERVER['PHP_SELF']);
            } else {
                Page::set('title', '创建导航 - '.Request::get()->id);
                Page::insertCallback('Admin-Content', function () {
                    Page::render('admin/nav-create');
                });
            }
            break;
            case 'modify':
                Page::set('title', '修改导航 - '.Request::get()->id);
                if (Request::post()->nav_set) {
                    Common_Navigation::update(Request::get()->id, Request::post()->nav_set);
                    header('Location:'.$_SERVER['PHP_SELF']);
                } else {
                    $nav=Common_Navigation::getNavById(Request::get()->id);
                    Page::set('nav', $nav);
                    Page::insertCallback('Admin-Content', function () {
                        Page::render('admin/nav-modify');
                    });
                }
            break;
            case 'delete':
                Common_Navigation::delete(Request::get()->id);
                header('Location:'.$_SERVER['PHP_SELF']);
            break;
            case 'sort':
            default:
            Page::set('title', '导航设置');
            Page::insertCallback('Admin-Content', function () {
                Page::set('navs', Common_Navigation::getNavsets());
                Page::render('admin/nav-sort');
            });
        }
    }
}
