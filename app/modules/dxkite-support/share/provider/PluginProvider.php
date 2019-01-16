<?php
namespace dxkite\support\provider;

use dxkite\support\plugin\Manager;

/**
 * 插件操作类
 */
class PluginProvider
{
    /**
     * 插件列表
     * 
     * @acl plugin.list
     * @return array
     */
    public function list():array
    {
        $list = Manager::instance()->getList();
        foreach ($list as $id => $item) {
            $list[$id] = [
                'unique' => $item->unique,
                'name' => $item->name,
                'icon' => $item->icon,
                'version' => $item->version,
                'discription' => $item->discription,
                'modules' => $item->modules?\array_keys($item->modules):[],
                'license' => $item->license,
                'author' => $item->author,
                'status' => $item->status,
            ];
        }
        return $list;
    }

    /**
     * 上传插件
     *
     * @acl plugin.upload
     * @param File $zip
     * @return boolean
     */
    public function upload(File $zip):bool
    {
        return Manager::instance()->upload($zip->getPath(), $zip->getName());
    }

    /**
     * 删除插件
     *
     * @acl plugin.delete
     * @param string $unique
     * @return boolean
     */
    public function delete(string $unique):bool
    {
        return Manager::instance()->delete($unique);
    }


    /**
     * 激活插件
     *
     * @acl plugin.active
     * @param string $unique
     * @return boolean
     */
    public function active(string $unique):bool
    {
        return Manager::instance()->active($unique);
    }

    /**
     * 禁用插件
     *
     * @acl plugin.deactivate
     * @param string $unique
     * @return boolean
     */
    public function deactivate(string $unique):bool
    {
        return Manager::instance()->deactivate($unique);
    }
}
