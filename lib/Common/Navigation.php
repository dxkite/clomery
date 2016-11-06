<?php

class Common_Navigation
{
    public static $navs;
    public static function init():bool
    {
        if (Cache::has('SiteNav')) {
            self::$navs=Cache::get('SiteNav');
        } else {
            return self::refresh();
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
        $q=new Query('SELECT `id`,`name` as `text`,`url`,`title` FROM `#{nav}` WHERE `show` = 1  ORDER BY `sort` ASC;');
        self::$navs=$q->fetchAll();
        Cache::set('SiteNav', self::$navs, 0);
        return $q->erron()===0;
    }
    public static function getNavset()
    {
        $q=new Query('SELECT `id`,`sort`,`name`,`url`,`title`,`show` FROM `#{nav}`  ORDER BY `sort` ASC;');
        return self::$navs=$q->fetchAll();
    }
    public static function addNavs(string $name, string $url, string $title, int $sort)
    {
        $sql='INSERT INTO `atd_nav` (`name`, `url`, `title`,`sort`) VALUES (:name,:url,:title,:sort);';
        return (new Query($sql, ['name'=>$name, 'title'=>$title, 'url'=>$url, 'sort'=>$sort]))->exec();
    }

    public static function update(int $id,array $set)
    {
        $set['id']=$id;
        $sql='UPDATE `#{nav}` SET `name`=:name ,`url`=:url ,`title`=:title ,`show`=:show ,`sort`=:sort WHERE  `id`= :id LIMIT 1;';
        // 偷懒
        return (new Query($sql,$set))->exec();
    }
}
