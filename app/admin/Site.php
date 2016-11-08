<?php 
namespace admin;

use Page;
use Core\Value;
use Request;

class Site extends \Admin_Autoentrance
{
    function run()
    {
        Page::set('title', '网站设置');
        if (Request::post()->site) {
            foreach (Request::post()->site as $key => $value) {
                var_dump(\Site_Options::setOption($key, $value));
            }
            header('Location:'.$_SERVER['PHP_SELF']);
        } else {
            $options=[];
            foreach (\Site_Options::getSiteOptions() as $option) {
                $options[$option['name']]=new Value($option);
            }
            Page::assign($options);
            Page::insertCallback('Admin-Content', function () {
                Page::render('admin/site');
            });
        }
    }
}
