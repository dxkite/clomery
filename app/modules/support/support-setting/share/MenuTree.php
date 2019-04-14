<?php
namespace support\setting;

use suda\framework\Config;
use suda\framework\Request;
use support\setting\Context;
use support\setting\Visitor;
use suda\application\Application;
use support\openmethod\Permission;

class MenuTree
{
    /**
     * Application
     *
     * @var Application
     */
    private $application;

    /**
     * Request
     *
     * @var Request
     */
    private $request;

    /**
     * Visitor
     *
     * @var Visitor
     */
    private $visitor;
    
    public function __construct(Context $context)
    {
        $this->application = $context->getApplication();
        $this->visitor = $context->getVisitor();
        $this->request = $context->getRequest();
    }

        
    public function getMenu(?string $select = null):array
    {
        $cache = $this->application->cache();
        if (!SUDA_DEBUG && $cache->has('support.setting_menu')) {
            return $cache->get('support.setting_menu');
        }
        $setting = $this->loadMenu($select);
        $cache->set('support.setting_menu', $setting);
        return $setting;
    }
    
    public function loadMenu(?string $select = null):array
    {
        $this->application->debug()->info('register setting menus ...');
        $adminsidebar = [];
        foreach ($this->application->getModules() as $fullName => $module) {
            if ($path = $module->getResource()->getConfigResourcePath('config/setting-menu')) {
                $adminsidebar[$fullName] = [];
                $sidebar = Config::loadConfig($path, [
                    'module' => $fullName,
                    'group' => 'setting',
                    'config' => $module->getConfig(),
                ]);
                if ($sidebar) {
                    $adminsidebar[$fullName] = array_merge($adminsidebar[$fullName], $sidebar);
                }
            }
        }
        $select = $select? $this->application->getRouteName($select, null, 'setting'):null;
        $viewObject = [];
        foreach ($adminsidebar as $module => $sidebar) {
            $view = $this->createSidebarView($sidebar, $module, $select);
            $viewObject = \array_merge($viewObject, $view);
        }
        return $this->sortSidebarView($viewObject);
    }
        
    protected function createSidebarView(array $adminSidebar, string $module, ?string $select = null)
    {
        $sidebarView = [];
        foreach ($adminSidebar as $parentName => $params) {
            if (\array_key_exists('acl', $params)) {
                if (!$this->visitor->hasPermission(new Permission($params['acl']))) {
                    continue;
                }
            }
            $sidebarItem = $this->createMenuItem($parentName, $params, $module, $select);
            $childs = [];
            if (\array_key_exists('child', $params)) {
                foreach ($params['child'] as $name => $value) {
                    if (\array_key_exists('acl', $value)) {
                        if (!$this->visitor->hasPermission(new Permission($value['acl']))) {
                            continue;
                        }
                    }
                    $child = $this->createMenuItem($name, $value, $module, $select);
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
    
    protected function createMenuItem(string $name, array $params, string $module, ?string $select = null)
    {
        $name = $this->application->getRouteName($name, $module, 'setting');
        return [
            'router' => $name,
            'name' => $params['name'],
            'acl' => $params['acl'] ?? [],
            'link' => $this->application->getUrl($this->request, $name, $params['args'] ?? [], true, $module, 'setting'),
            'sort' => $params['sort'] ?? 0,
            'select' => $select === $name,
        ];
    }
    
    
    protected function sortSidebarView($list)
    {
        $list = $this->sort($list);
        foreach ($list as $id => $item) {
            if (\array_key_exists('child', $list)) {
                $list[$id]['child'] = $this->sort($item['child']);
            }
        }
        return $list;
    }
     
    protected function sort(array $array)
    {
        usort($array, function ($a, $b) {
            if (\array_key_exists('child', $a) && \array_key_exists('child', $b)) {
                return  - ($a['sort'] - $b['sort']);
            }
            return 0;
        });
        return $array;
    }
}
