<?php
namespace admin;

use Page;
use Core\Caller;
use Core\Value;
use Request;
use Common_Navigation;

class Index extends \Admin_Autoentrance
{
    public function entrance(string $addon)
    {
       // For plugin entrance
    }
    
    public function run()
    {
         $infos=[
                    'debug_mod'=>conf('DEBUG')?'true':'false',
                    'core_ver'=>CORE_VERSION,
                    'php_ver'=>PHP_VERSION,
                    'img_ver'=>conf('Driver.Image').'/'.\Image::version(),
                    
                    'db_ver'=>\Database::version(),
                    'user_num'=>\Common_User::count(),
                    'article_num'=>\Blog_Article::countPublic(),
                    'env'=>$_SERVER['SERVER_SOFTWARE'],
                    
                    'upload_max'=>ini_get('upload_max_filesize'),
                    'safe_mode'=>ini_get('safe_mode')?'true':'false',
                ];

        Page::insertCallback('Admin-Content', function () {
            Page::render('admin/main');
        });
                
        Page::assign($infos);
    }
}
