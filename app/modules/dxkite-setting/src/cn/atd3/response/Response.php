<?php
namespace cn\atd3\response;

use cn\atd3\visitor\Context;
use suda\core\Query;

class Response extends \cn\atd3\user\response\OnUserVisitorResponse
{

    /**
     * 列出网站信息
     * 
     * @acl admin:website.info
     * 
     * @param Context $context
     * @return void
     */
    public function onUserVisit(Context $context)
    {
        $page=$this->page('index');
        $page->set('version.server', $_SERVER["SERVER_SOFTWARE"]);
        $page->set('version.php', PHP_VERSION);
        $page->set('version.mysql', self::getMySQLVersion());
        $page->set('version.gd', self::getGDVersion());
        $page->set('version.suda', SUDA_VERSION);
        $page->set('upload', self::getFileupload());
        $page->render();
    }

    public function getFileupload()
    {
        return ini_get("file_uploads") ? ini_get("upload_max_filesize"):__('不支持文件上传');
    }
    
    public function getMySQLVersion()
    {
        return (new Query('select version() as version'))->fetch()['version']??'unkown';
    }

    public function getGDVersion()
    {
        if (function_exists("gd_info")) {
            $gd = gd_info();
            $gdinfo = $gd['GD Version'];
        } else {
            $gdinfo = __('未知');
        }
        return $gdinfo;
    }
}
