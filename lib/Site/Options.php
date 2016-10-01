<?php
namespace Site;

use \Query;  // 数据库查询
use \Cache;  // 缓存

class Options
{
    public static $options;
    public static function init()
    {
        if (Cache::has('SiteOption')) {
            $options=Cache::get('SiteOption');
        } elseif (self::refresh()) {
            $options=self::$options;
        } else {
            return false;
        }
        foreach ($options as $option) {
            self::$options[$option['name']]=$option['value'];
        }
        return true;
    }

    /**
     * @return mixed
     */
    public static function getOptions()
    {
        return self::$options;
    }
    public static function getTheme()
    {
        return self::$options['theme'];
    }
    public static function getSitename()
    {
        return self::$options['site_name'];
    }
    public static function refresh()
    {
        $sql='SELECT * FROM `#{site_options}`';
        $q=new Query($sql);
        self::$options=$q->fetchAll();
        Cache::set('SiteOption', self::$options, 0);
        return $q->erron()===0;
    }
    public function  __get(string $name)
    {
        return self::$options[$name];
    }
}
