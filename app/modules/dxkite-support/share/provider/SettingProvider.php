<?php
namespace dxkite\support\provider;

use dxkite\support\view\TablePager;
use dxkite\support\table\setting\SettingTable;

class SettingProvider
{
    protected static $continents = [ 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'];
    
    /**
     * 获取设置菜单
     * 
     * @acl website.list-setting
     * @param-source get,json
     * @param string|null $select 当前设置路由
     * @return array
     */
    public function getSettingMenu(?string $select=null):array
    {
        return \dxkite\support\setting\View::loadSettingMenu($select);
    }

    /**
     * 获取设置列表
     *
     * @acl website.list-setting
     * @param integer|null $page
     * @param integer $row
     * @return array
     */
    public function getSettings(?int $page=1, int $row=10): array
    {
        return TablePager::listWhere(new SettingTable, '1', [], $page, $row);
    }

    /**
     * 设置
     *
     * @acl website.edit-setting
     * @param string $name
     * @param [type] $value
     * @return void
     */
    public function setSetting(string $name,  $value)
    {
        return setting_set($name, $value);
    }

    /**
     * 获取环境配置
     *
     * @acl website.info
     * @return array
     */
    public function getEnviroments():array
    {
        return [
            [
                'name' => __('服务器'),
                'value' => $_SERVER["SERVER_SOFTWARE"],
            ],
            [
                'name' => __('PHP版本'),
                'value' => PHP_VERSION,
            ],
            [
                'name' => __('GD图形库'),
                'value' => self::getGDVersion(),
            ],
            [
                'name' => __('MySQL数据库'),
                'value' => self::getMySQLVersion(),
            ],
            [
                'name' => __('框架版本'),
                'value' => SUDA_VERSION,
            ],
            [
                'name' => __('文件上传限制'),
                'value' => self::getFileupload(),
            ],
            [
                'name' => __('时区'),
                'value' => setting('timezone', 'PRC'),
            ]
        ];
    }
    
    /**
     * 获取时区
     *
     * @acl website.list-setting
     * @param string|null $current
     * @return array
     */
    public function getTimezone(?string $current=null):array
    {
        $timezone=[];
        $select=$current?$current:setting('timezone', 'PRC');
        $prc = [
            'name' => __('PRC'),
            'value' => 'PRC',
        ];
        if ($select === $prc['value']) {
            $prc['select'] = true;
        }
        $timezone['Global'][] = $prc;
        $utc = [
            'name' => __('UTC'),
            'value' => 'UTC',
        ];
        if ($select === $utc['value']) {
            $utc['select'] = true;
        }
        $timezone['Global'][] = $utc;
        foreach (timezone_identifiers_list() as $item) {
            $zone=explode('/', $item);
            if (!in_array($zone[0], self::$continents)) {
                continue;
            }
            $area = ['name'=> __($zone[1]) ,'value'=>$item ];
            if ($select === $area['value']) {
                $area['select'] = true;
            }
            $timezone[$zone[0]][]= $area;
        }
        return $timezone;
    }

    protected function getFileupload()
    {
        return ini_get("file_uploads") ? ini_get("upload_max_filesize"):__('不支持文件上传');
    }
    
    protected function getMySQLVersion()
    {
        return query('select version() as version')->fetch()['version']??'unkown';
    }

    protected function getGDVersion()
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
