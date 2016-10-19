<?php

class Database
{
    public static function import(string $import)
    {
        if (Storage::exist($import)) {
            return require $import;
        }
        return false;
    }
    
    public static function export(string $export, array $saves_table=[])
    {
        $version=CORE_VERSION;
        $date=date('Y-m-d H:i:s');
        $host=isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';
        $datebase=conf('Database.dbname');
        $tables=($q=new Query("show tables;"))->fetchAll();
        $tables_count=count($tables);
        $server_version=(new Query('select version() as version;'))->fetch()['version'];
        $head=<<< Table
<?php
/* ------------------------------------------------------ *\
   ------------------------------------------------------
   PHP Simple Library XCore $version Database Backup File
        Create On: $date
        SQL Server version: $server_version
        Host: $host   
        Database: $datebase
        Tables: $tables_count
   ------------------------------------------------------
\* ------------------------------------------------------ */

try {
/** Open Transaction Avoid Error **/
Query::beginTransaction();

Table;
        $export_str=$head;
        foreach ($tables as $table_array) {
            $tablename=current($table_array);
            preg_match('/^'.conf('Database.prefix').'(.+?)$/', $tablename, $tbinfo);
            $export_str.=self::querySQLString('DROP TABLE IF EXISTS #{'.$tbinfo[1].'}');
            $export_str.=self::querySQLTableStruct(current($table_array));
            // 0 全部 有则保存指定的
            if (count($saves_table)===0) {
                $export_str.=self::querySQLTableValues(current($table_array));
            } elseif (in_array($tbinfo[1], $saves_table)) {
                $export_str.=self::querySQLTableValues(current($table_array));
            }
        }
        $end=<<< 'End'
/** End Querys **/
Query::commit();
return true;
} 
catch (Exception $e)
{
    Query::rollBack();
   return false;
}
End;
        $export_str.=$end;
        return Storage::put($export, $export_str);
    }
    public static function exportSQL(string $output, array $saves_table=[])
    {
        $version=CORE_VERSION;
        $date=date('Y-m-d H:i:s');
        $host=isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';
        $datebase=conf('Database.dbname');
        $server_version=(new Query('select version() as version;'))->fetch()['version'];
        $head=<<< Table
-- ----------------------------------------------------------
-- PHP Simple Library XCore $version Database Backup File
-- Create On $date
-- Host: $host   Database: $datebase
-- Server version	$server_version
-- ------------------------------------------------------
/*!40101 SET NAMES utf8 */;


Table;
        Storage::put($output, $head);
        return self::saveSQLTables($output, $saves_table);
    }

    public static function querySQLTableStruct(string $table)
    {
        if ($struct=self::getTableStruct($table)) {
            $struct=preg_replace('/^CREATE TABLE `'.conf('Database.prefix').'(.+?)`/', 'CREATE TABLE `#{$1}`', $struct);
            return self::querySQLString($struct);
        }
        return '/* Error Export Table Struct : '.$table.' */';
    }

    public static function querySQLString(string $sql)
    {
        return ' (new Query(\''.addslashes($sql).'\'))->exec();'."\r\n\r\n";
    }
    
    public static function saveSQLTables(string $fileout, array $saves_table=[])
    {
        $tables=($q=new Query("show tables;"))->fetchAll();
        foreach ($tables as $table) {
            $table=current($table);
            $doc=<<< Table
--
-- Create Table $table
--
Table;
            if ($str=self::getTableStruct($table)) {
                $sql='DROP TABLE IF EXISTS `'.$table.'`;'."\r\n";
                Storage::put($fileout, $doc."\r\n\r\n".$sql.$str.";\r\n\r\n\r\n", FILE_APPEND);
                // var_dump($table, $saves_table);
                preg_match('/^'.conf('Database.prefix').'(.+?)$/',$table, $tbinfo);
                // 0 全部 有则保存指定的
                if (count($saves_table)===0) {
                    self::saveSQLData($fileout, $table);
                } elseif (in_array($tbinfo[1], $saves_table)) {
                    self::saveSQLData($fileout, $table);
                }
            } else {
                return false;
            }
        }
        return true;
    }
    public static function saveSQLData(string $file, string $table)
    {
        $q=new Query('SELECT * FROM '.$table.' WHERE 1;', [], true);
        if ($q) {
            //$sql="\r\n\r\nLOCK TABLES `$table` WRITE;\r\n/*!40000 ALTER TABLE `$table` DISABLE KEYS */;\r\n".'INSERT INTO `'.$table.'` VALUES ';
            $sql="\r\n\r\n".'INSERT INTO `'.$table.'` VALUES ';
            Storage::put($file, $sql, FILE_APPEND);
            $first=true;
            while ($values=$q->fetch()) {
                $sql='';
                if ($first) {
                    $first=false;
                } else {
                    $sql.=',';
                }
                $sql.='(';
                $columns='';
                foreach ($values as $val) {
                    $columns.='\''.addslashes($val).'\',';
                }
                $columns=rtrim($columns, ',');
                $sql.= $columns;
                $sql.=')';
                Storage::put($file, $sql, FILE_APPEND);
            }
            Storage::put($file, ";\r\n\n\r\n", FILE_APPEND);
            //Storage::put($file, ";\r\n/*!40000 ALTER TABLE `atd_comment` ENABLE KEYS */;\r\nUNLOCK TABLES;\r\n\r\n", FILE_APPEND);
        }
        else{
            Storage::put($file, "/** Table {$table}  Save Failed **/\r\n\n\r\n", FILE_APPEND);
        }
    }
    
    public static function querySQLTableValues(string $table)
    {
        if ($sql=self::getTableValues($table)) {
            $sql=preg_replace('/^INSERT INTO `'.conf('Database.prefix').'(.+?)`/', 'INSERT INTO  `#{$1}`', $sql);
            return self::querySQLString($sql);
        }
        return '/* Table ' .$table .'\'s Values Cann\'t Get */';
    }

    public static function getTableValues(string $table)
    {
        $q=new Query('SELECT * FROM '.$table.' WHERE 1;', [], true);
        if ($q) {
            $sqlout='INSERT INTO `'.$table.'` VALUES ';
            $first=true;
            while ($values=$q->fetch()) {
                $sql='';
                if ($first) {
                    $first=false;
                } else {
                    $sql.=',';
                }
                $sql.='(';
                $columns='';
                foreach ($values as $val) {
                    $columns.='\''.addslashes($val).'\',';
                }
                $columns=rtrim($columns, ',');
                $sql.= $columns;
                $sql.=')';
                $sqlout.=$sql;
            }
            return $sqlout;
        }
        return false;
    }
    public static function getTableStruct(string $table)
    {
        $table_info=($q=new Query("show create table {$table};"))->fetch();
        if ($table_info) {
            return $table_info['Create Table'];
        }
        return false;
    }
}
