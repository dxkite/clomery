<?php
namespace dxkite\support\setting;

use suda\core\Router;
use suda\core\Application;
use suda\tool\Json;
use suda\core\Storage;
use suda\template\compiler\suda\Compiler;
use dxkite\support\visitor\Context;
use dxkite\support\visitor\Permission;
use suda\core\route\Mapping;

class View
{
    private static $namespace = null;
    private static $adminsidebar=[];
    private static $adminSidebarTree=null;
    private static $childsidebar=null;
    
    public static function getNamespace():string
    {
        if (\is_null(self::$namespace)) {
            self::$namespace = conf('support.setting-path', '/setting');
        }
        return self::$namespace;
    }

    public static function getSettingMenu(?string $select=null):array
    {
        if (!conf('debug', false) && cache()->has('support.setting_menu')) {
            return cache()->get('support.setting_menu');
        }
        $setting = self::loadSettingMenu($select);
        cache()->set('support.setting_menu', $setting);
        return $setting;
    }


    public static function registerAdminSidebarRouter(Router $router)
    {
        debug()->info('add admin sidebar');
        $modules=app()->getLiveModules();
        foreach ($modules as $module) {
            self::registerSidebarRouterConfig($module, $router);
        }
    }

    private static function registerSidebarRouterConfig(string $module, Router $router)
    {
        $routers=app()->getModuleConfig($module, 'setting/router');
        if (is_array($routers)) {
            debug()->trace('load sidebar router ['.$module.']');
            foreach ($routers as $name=>$router_info) {
                $fix=preg_replace('/:(.+)$/', '', $module);
                $autoPrefix=$router_info['anti-prefix']??false;
                $moduleurl=($autoPrefix)?self::getNamespace():self::getNamespace().'/'.$fix;
                $mapping=Mapping::createFromRouteArray(Mapping::DEFAULT_GROUP, $module, $name, $router_info);
                $mapping->setUrl(rtrim($moduleurl.$mapping->getUrl(), '/'));
                $mapping->setDynamic();
                $mapping->build();
                $router->addMapping($mapping);
            }
        }
    }


    public static function hook(Compiler $compiler)
    {
        $compiler->addCommand('time', function ($expr) {
            return '<?php echo '.__CLASS__ ."::time{$expr}; ?>";
        });
    }

    public static function time(int $time)
    {
        $text = '';
        $time = $time === null || $time > time() ? time() : intval($time);
        $t = time() - $time;
        $y = date('Y', $time)-date('Y', time());
        switch ($t) {
         case $t < 20:
           $text = __('刚刚');
           break;
        case $t < 60:
          $text = __('$0秒前', $t);
          break;
        case $t < 60 * 60:
          $text = __('$0分钟前', floor($t / 60));
          break;
        case $t < 60 * 60 * 24:
          $text = __('$0小时前', floor($t / (60 * 60)));
          break;
        case $t < 60 * 60 * 24 * 3:
          $text = floor($time/(60*60*24)) ==1 ? date(__('昨天 H:i'), $time) : date(__('前天 H:i'), $time);
          break;
        case $t < 60 * 60 * 24 * 30:
          $text = date(__('m月d日 H:i'), $time);
          break;
        case $t < 60 * 60 * 24 * 365&&$y==0:
          $text = date(__('m月d日'), $time);
          break;
        default:
          $text = date(__('Y年m月d日 H:i:s'), $time);
          break;
        }
        return $text;
    }


    public static function loadSettingMenu(?string $select=null):array
    {
        $modules=app()->getReachableModules();
        $adminsidebar =[];
        foreach ($modules as $module) {
            $sidebar=app()->getModuleConfig($module, 'setting/sidebar');
            if ($sidebar) {
                $adminsidebar=array_merge($adminsidebar, $sidebar);
            }
        }
        $select = $select?router()->getRouterFullName($select):null;
        $viewObject = self::createSidebarView($adminsidebar, $select);
        return self::sortSidebarView($viewObject);
    }
    
    protected static function createSidebarView(array $adminSidebar, ?string $select=null)
    {
        $sidebarView = [];
        foreach ($adminSidebar as $parentName => $params) {
            if (\array_key_exists('acl', $params)) {
                if (!\visitor()->hasPermission(new Permission($params['acl']))) {
                    continue;
                }
            }
            $sidebarItem = self::createMenuItem($parentName, $params, $select);
            $childs = [];
            if (\array_key_exists('child', $params)) {
                foreach ($params['child'] as $name => $value) {
                    if (\array_key_exists('acl', $value)) {
                        if (!\visitor()->hasPermission(new Permission($value['acl']))) {
                            continue;
                        }
                    }
                    $child = self::createMenuItem($name, $value, $select);
                    if ($child['select'] === true) {
                        $sidebarItem['select'] = true;
                    }
                    $childs[] = $child;
                }
            }
            $sidebarItem['child'] = $childs;
            $sidebarView[] = $sidebarItem;
        }
        return $sidebarView;
    }

    protected static function createMenuItem(string $name, array $params, ?string $select=null)
    {
        $name = router()->getRouterFullName($name);
        return [
            'router' => $name,
            'name' => $params['name'],
            'acl' => $params['acl'] ?? [],
            'link' => u($name, $params['args'] ??[]),
            'sort' => $params['sort'] ?? 0,
            'select' => $select === $name,
        ];
    }


    protected static function sortSidebarView($list)
    {
        $list=self::sort($list);
        foreach ($list as $id => $item) {
            if (\array_key_exists('child', $list)) {
                $list[$id]['child']=self::sort($item['child']);
            }
        }
        return $list;
    }
 
    protected static function sort(array $array)
    {
        usort($array, function ($a, $b) {
            if (\array_key_exists('child', $a) && \array_key_exists('child', $b)) {
                return $a['sort']-$b['sort'];
            }
            return 0;
        });
        return $array;
    }
}
