<?php
namespace admin;

use Page;
use Core\Caller;
use Core\Value;


class Index
{
    public function entrance($addon='')
    {
    }
    
    public function main()
    {
        if (\System::user()->hasSignin) {
            if (\System::user()->permission->editSite) {
                // Page::set('admin_entrance');
                $options[]=new Value(['title'=>'网站设置', 'href'=>Page::url('admin', ['path'=>'site'])]);
                $options[]=new Value(['title'=>'导航栏设置', 'href'=>Page::url('admin', ['path'=>'navigation'])]);
                $infos=[
                    'debug_mod'=>conf('DEBUG')?'true':'false',
                    'core_ver'=>CORE_VERSION,
                    'php_ver'=>PHP_VERSION,
                    'img_ver'=>conf('Driver.Image').'-'.\Image::version(),
                    
                    'db_ver'=>\Database::version(),
                    'user_num'=>\Common_User::numbers(),
                    'article_num'=>\Blog_Article::numbers(),
                    'env'=>$_SERVER['SERVER_SOFTWARE'],
                    
                    'upload_max'=>ini_get('upload_max_filesize'),
                    'safe_mode'=>ini_get('safe_mode')?'true':'false',
                ];

                Page::assign($infos);
                Page::set('options', $options);
                Page::use('admin/index');
            } else {
                echo 'no perimission';
            }
        } else {
            Page::redirect('/user/SignIn');
        }
    }
}
