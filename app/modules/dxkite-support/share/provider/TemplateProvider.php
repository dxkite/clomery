<?php
namespace dxkite\support\provider;

use suda\tool\ZipHelper;
use dxkite\support\file\File;
use dxkite\support\template\Data;
use dxkite\support\template\Manager;

/**
 * 模板操作类
 */
class TemplateProvider
{
    /**
     * 上传模板
     * 
     * @acl template.upload
     * @param File $zip 模板文件
     * @return boolean
     */
    public function upload(File $zip) :bool
    {
        return Manager::instance()->upload($zip->getPath(), $zip->getName());
    }

    /**
     * 设置模板
     * 
     * @acl template.set
     * @param string $templateName
     * @return boolean
     */
    public function set(string $unique): bool
    {
        \setting_set('template',$unique);
        return Manager::instance()->changeTheme($unique);
    }

    /**
     * 下载模板
     * 
     * @param-source get,json
     * @acl template.download
     * @param string $unique
     * @return File|null
     */
    public function download(string $unique):?File
    {
        $ouptut=TEMP_DIR.'/export-'.$unique;
        Manager::exportTemplate($unique, $ouptut);
        $tempFile= TEMP_DIR.'/export/template_'.$unique.'.zip';
        storage()->path(TEMP_DIR.'/export');
        if (ZipHelper::zip($ouptut, $tempFile)) {
            storage()->delete($ouptut);
            $file = new File($tempFile, true);
            return $file;
        }
        return null;
    }

    /**
     * 删除模板
     *
     * @acl template.delete
     * @param string $unique
     * @return boolean
     */
    public function delete(string $unique):bool
    {
        return Manager::instance()->delete($unique);
    }

    /**
     * 模板列表
     *
     * @acl template.list
     * @return array
     */
    public function list():array
    {
        $list=  Manager::instance()->getTemplateList();
        $current = setting('template');
        foreach ($list as $id => $item) {
            $list[$id] = [
                'unique' => $item->uniqid,
                'name' => $item->name,
                'icon' => $item->icon,
                'version' => $item->version,
                'discription' => $item->discription,
                'modules' => \array_keys($item->modules),
                'license' => $item->license,
                'author' => $item->author,
                'current' => $item->uniqid === $current ,
            ];
        }
        return $list;
    }

    /**
     * 刷新模板资源
     *
     * @acl template.refresh
     * @param string $module
     * @return array
     */
    public function refresh(string $module): array
    {
        return current(init_resource([$module]));
    }

    /**
     * 获取模板可导出表
     *
     * @acl template.data-table-list
     * @return array
     */
    public function getDataTables(): array
    {
        return (new Data)->getTemplateTables();
    }

    /**
     * 导出数据表
     * 
     * @acl template.data-table-export
     * @param string $table
     * @return boolean
     */
    public function exportDataTable(string $table)
    {
        return (new Data)->exportTemplateDataTable($table);
    }

    /**
     * 导入数据表
     * 
     * @acl template.data-table-import
     * @param string $table
     * @return boolean
     */
    public function importDataTable(string $table)
    {
        return (new Data)->importTemplateDataTable($table);
    }
}
