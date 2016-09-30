<?php
namespace Site;

use \Query;  // 数据库查询
use \Cache;  // 缓存

class NavOp
{
    public static $navs;
    public static function init():bool
    {
        if (Cache::has('SiteNav')) {
            self::$navs=Cache::get('SiteNav');
        } else {
            $q=new Query('SELECT `id`,`name` as `text`,`url` FROM `#{nav}` WHERE `show` =1 ORDER BY `sort` ASC;');
            self::$navs=$q->fetchAll();
            Cache::set('SiteNav', self::$navs, 0);
            return $q->erron()===0;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public static function getNavs()
    {
        return self::$navs;
    }
    public static function refresh()
    {
        $q=new Query('SELECT `id`,`name` as `text`,`url` FROM `#{nav}` WHERE `show` =1  ORDER BY `sort` ASC;');
        self::$navs=$q->fetchAll();
        Cache::set('SiteNav', self::$navs, 0);
        return $q->erron()===0;
    }
    public static function addNavs()
    {
        $sql='INSERT INTO `atd_nav` (`name`, `url`, `sort`) VALUES (:name,:url,:sort);';
    }
}
