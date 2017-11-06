<?php
namespace cn\atd3\admin\sidebar;

use suda\core\Router;
use suda\core\Application;
use suda\tool\Json;
use suda\core\Storage;
use suda\template\compiler\suda\Compiler;


class Creator
{
    private static $namespace='/setting';
    private static $adminsidebar=[];
    private static $childsidebar=null;


    public static function addAdminSidebar(Router $router)
    {
        debug()->info('add admin sidebar');
        $modules=app()->getLiveModules();
        foreach ($modules as $module) {
            self::addModuleAdminSidebar($module, $router);
        }
    }

    public static function hook(Compiler $compiler)
    {
        $compiler->addCommand('isChild',function($expr){
            return "<?php echo cn\atd3\admin\sidebar\Creator::isChild{$expr}; ?>";
        });
    }

    public static function isChild(string $parent, string $child,string $true,string $false='')
    {
        $parent=Router::getInstance()->getRouterFullName($parent);
        $child=Router::getInstance()->getRouterFullName($child);
        if (isset(self::$childsidebar[$parent])) {
            return in_array($child, self::$childsidebar[$parent])?$true:$false;
        }
        return $false;
    }
    /**
     * 压入管理侧边栏
     *
     * @param [type] $template
     * @return void
     */
    public static function renderAdminSidebar($template)
    {
        // 树状结构化
        $sidebar=self::adminSidebarTree(self::$adminsidebar);
        // 排序侧边栏
        $sidebar=self::adminSidebarSort($sidebar);
        // 压入模板内存
        $template->assign(['admin'=>['sidebar'=>$sidebar]]);
    }

    private static function addModuleAdminSidebar(string $module, Router $router)
    {
        self::addSidebarConfig($module);
        self::addSidebarRouterConfig($module, $router);
    }

    private static function addSidebarRouterConfig(string $module, Router $router)
    {
        if (storage()->exist($path=app()->getModulePath($module).'/resource/config/admin/router.json')) {
            $routers=Json::loadFile($path);
            foreach ($routers as $name=>$router_info) {
                $fix=preg_replace('/:(.+)$/', '', $module);
                // 去除版本
                $moduleurl=($router_info['anti-fix']??false)?self::$namespace:self::$namespace.'/'.$fix;
                // 添加路由
                $router->addRouter( $name,rtrim( $moduleurl.$router_info['visit'] ,'/'), $router_info['class'], $module, $router_info['method']??[]);
            }
        }
    }

    private static function addSidebarConfig(string $module)
    {
        if (storage()->exist($jsonfile=app()->getModulePath($module).'/resource/config/admin/sidebar.json')) {
            $configs=Json::loadFile($jsonfile);
            self::$adminsidebar=array_merge(self::$adminsidebar, $configs);
        }
    }


     // 管理侧边栏格式调整
     private static function adminSidebarTree(array $sidebarconfig)
     {
         $sidebar=[];
         // 调整侧边栏格式
         foreach ($sidebarconfig as $name=>$adminsidebar) {
             $id=count($sidebar);
             $sidebar[$id]['text']=__($adminsidebar['name']);
             $sidebar[$id]['href']=u($name, $adminsidebar['args']??[]);
             $sidebar[$id]['id']=$name;
             $sidebar[$id]['sort']=$adminsidebar['sort']??0;
             // 二级菜单
             if (isset($adminsidebar['child']) && is_array($adminsidebar['child'])) {
                 $parent=Router::getInstance()->getRouterFullName($name);
                 foreach ($adminsidebar['child'] as $name=>$child) {
                     $cid=count($sidebar[$id]['child']??[]);
                     self::$childsidebar[$parent][]=Router::getInstance()->getRouterFullName($name);
                     $sidebar[$id]['child'][$cid]['text']=__($child['name']);
                     $sidebar[$id]['child'][$cid]['href']=u($name, $child['args']??[]);
                     $sidebar[$id]['child'][$cid]['id']=$name;
                     $sidebar[$id]['child'][$cid]['sort']=$child['sort']??0;
                 }
             }
         }
         return $sidebar;
     }
 
     /**
      * 按索引升序排列
      *
      * @param [type] $list
      * @return void
      */
     private static function adminSidebarSort($list)
     {
         $list=self::sort($list);
         foreach ($list as $id=>$item) {
             if (isset($item['child'])) {
                 $list[$id]['child']=self::sort($item['child']);
             }
         }
         return $list;
     }
 
    private static function sort(array $array)
    {
        uasort($array, function ($a, $b) {
            if (isset($a['sort']) && isset($b['sort'])) {
                return $a['sort']-$b['sort'];
            }
            return 0;
        });
        return $array;
    }
}
