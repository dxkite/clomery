<?php
namespace cn\atd3\database;

use cn\atd3\proxy\ProxyObject;
use suda\archive\SQLQuery;
use suda\archive\TableInstance;

class DbHelper extends ProxyObject
{
    const PATH=DATA_DIR.'/backup';

    public function status()
    {
        $tables= TableInstance::instance()->getTables();
        $tableInfos=[];
        foreach ($tables  as $name=>$class) {
            $tableName = TableInstance::getInstance($name)->getTableName();
            $status= (new SQLQuery('SHOW TABLE STATUS LIKE \'#{'.$tableName.'}\''))->fetch();
            $tableInfo=new TableInfo;
            $tableInfo->realName=$status['Name'];
            $tableInfo->rows=$status['Rows'];
            $tableInfo->create=$status['Create_time'];
            $tableInfo->size=$status['Data_length'];
            $tableInfo->name=$name;
            $tableInfos[]=$tableInfo;
        }
        return $tableInfos;
    }
    
    public function create()
    {
        $hash=date('Y-m-d_H.i.s_').substr(md5(time()),0,8);
        storage()->path(self::PATH.'/'.$hash);
        $config=new Config;
        $config->create=time();
        $config->tables=[];
        $config->modify=time();
        storage()->put(self::PATH.'/'.$hash.'/config.json',json_encode($config));
        return $hash;
    }

    public function deleteBackup(string $hash)
    {
        if (storage()->exist($configFile=self::PATH.'/'.$hash.'/config.json')){
            return storage()->delete(self::PATH.'/'.$hash);
        }else{
            return false;
        }
    }

    public function backupTo(string $hash, string  $table)
    {
        if (storage()->exist($configFile=self::PATH.'/'.$hash.'/config.json')){
            $content=storage()->get($configFile);
            $config=json_decode($content);
            $config->modify=time();
            if (!in_array($table,$config->tables)){
                $config->tables[]=$table;
            }
            table($table)->export(self::PATH.'/'.$hash.'/'.$table.'.base64');
            storage()->put($configFile,json_encode($config));
            return true;
        }else{
            return false;
        }
    }

    public function importFrom(string $hash, string $table)
    {
        if (storage()->exist($configFile=self::PATH.'/'.$hash.'/config.json')){
            return table($table)->import(self::PATH.'/'.$hash.'/'.$table.'.base64');
        }else{
            return false;
        }
    }

    public function optimize(array $tables)
    {
        foreach ($tables  as $name) {
            $tableArr[]='`#{'.$name.'}`';
        }
        return (new SQLQuery('OPTIMIZE TABLE '. implode(',',$tableArr)))->exec();
    }

    public function repair(array $tables)
    {
        foreach ($tables  as $name) {
            $tableArr[]='`#{'.$name.'}`';
        }
        return (new SQLQuery('REPAIR TABLE '. implode(',',$tableArr)))->exec();
    }

    public function list()
    {
        $readDirs=storage()->readDirs(self::PATH);
        $configs=[];
        foreach ($readDirs as $dir) {
            $config_path=self::PATH.'/'.$dir.'/config.json';
            if (storage()->exist($config_path)) {
                $conf=storage()->get($config_path);
                $configs[$dir]= json_decode($conf);
            }else{
                storage()->delete(self::PATH.'/'.$dir);
            }
        }
        return $configs;
    }
}
