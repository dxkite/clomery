<?php
namespace dxkite\support\provider;

use suda\tool\ZipHelper;
use dxkite\support\file\File;
use dxkite\support\database\DbHelper;

class DatabaseProvider
{

    /**
     * 列出数据表信息
     *
     * @acl database.list_tables
     * @return void
     */
    public static function status()
    {
        return DbHelper::status();
    }

    /**
     * 创建数据库备份
     *
     * @acl database.create_backup
     * @return string
     */
    public static function create()
    {
        return DbHelper::create();
    }
    
    /**
     * 删除备份
     *
     * @acl database.delete_backup
     * @param string $hash
     * @return boolean
     */
    public static function delete(string $hash)
    {
        return DbHelper::deleteBackup($hash);
    }
    
    
    /**
     * 备份表到一备份数据
     *
     * @acl database.export_table
     * @param string $hash
     * @param string $table
     * @return boolean
     */
    public static function backupTo(string $hash, string  $table)
    {
        return DbHelper::backupTo($hash, $table);
    }
    
    /**
     * 从某一备份中导入数据
     *
     * @acl database.import_table
     * @param string $hash
     * @param string $table
     * @return void
     */
    public static function importFrom(string $hash, string $table)
    {
        return DbHelper::importFrom($hash, $table);
    }
    
    /**
     * 从某一备份中强制导入数据
     *
     * @acl database.import_table
     * @param string $hash
     * @param string $table
     * @return void
     */
    public static function forceImportFrom(string $hash, string $table)
    {
        return DbHelper::forceImportFrom($hash, $table);
    }
    
    /**
     * 优化数据表
     * @acl database.optimize_tables
     * @param array $tables
     * @return void
     */
    public static function optimize(array $tables)
    {
        return DbHelper::optimize($tables);
    }
    
    /**
     * 修复数据表
     * @acl database.repair_tables
     * @param array $tables
     * @return void
     */
    public static function repair(array $tables)
    {
        return DbHelper::optimize($tables);
    }
    
    /**
     * 列出备份
     * @acl database.list_backups
     * @return void
     */
    public static function list()
    {
        return DbHelper::list();
    }
   
    /**
     * 上传备份
     * @acl database.upload
     * @param File $backupFile
     * @return void
     */
    public static function upload(File $backupFile)
    {
        return DbHelper::upload($backupFile);
    }

    /**
     * 查看备份信息
     *
     * @acl database.backup_info
     * @param string $hash
     * @return void
     */
    public static function backupInfo(string $hash)
    {
        return DbHelper::backupInfo($hash);
    }

    /**
     * 下载数据备份
     * 
     * @acl database.download
     * @param-source get,json
     * @param string $unique
     * @return File|null
     */
    public function download(string $unique):?File
    {
        if ($path=DbHelper::backupPath($unique)) {
            $tempFile= TEMP_DIR.'/export/database_'.$unique.'.zip';
            storage()->path(TEMP_DIR.'/export');
            if (ZipHelper::zip($path, $tempFile)) {
                $file = new File($tempFile, true);
                return $file;
            }
        }
        return null;
    }
}
