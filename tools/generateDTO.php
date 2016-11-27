<?php
require_once __DIR__.'/../system/initailze.php';
defined('DTA_TPL') or define('DTA_TPL', __DIR__ .'/tpl');
use template\Builder as TplBuilder;
use archive\DTOReader;

function compileAll()
{
    $files=Storage::readDirFiles(DTA_TPL, true, '/\.raw$/');
    foreach ($files as $file) {
        $text=(new TplBuilder())->compileText(file_get_contents($file));
        $to=preg_replace('/\.raw/', '.tpl', $file);
        file_put_contents($to, $text);
    }
}

function tablename($namespace, $name)
{
    return ($name===$namespace?$name:preg_replace_callback('/(\\\\|[A-Z])/', function ($match) {
        if ($match[0]==='\\') {
            return '_';
        } else {
            return '_'.strtolower($match[0]);
        }
    }, $namespace.'\\'.$name));
}
compileAll();

$params=array_slice($argv, 1);
$src=isset($params[0])?$params[0]:SITE_RESOURCE.'/dto';
$dist=isset($params[1])?$params[1]:SITE_LIB;
$outsql=isset($params[2])?$params[2]:$dist.'/database_create.sql';
$tables=Storage::readDirFiles($src, true, '/\.dto$/', true);
file_put_contents($outsql, '-- create:'.date('Y-m-d H:i:s')."\r\n");

foreach ($tables as $table) {
    $name=pathinfo($table, PATHINFO_FILENAME);
    $namespace=preg_replace('/\\\\\//', '\\', dirname($table));
    $table_name=tablename($namespace, $name);
    if ($namespace!==$name) {
        $namespace='dto\\'.$namespace;
    } else {
        $namespace='dto';
    }
    
    $name=ucfirst($name);
    $builder=new DTOReader;
    $builder->load($src.'/'.$table);
    $builder->setName($name);
    $builder->setNamespace($namespace);
    $builder->setTableName($table_name);
    // $output=$dist.'/'.preg_replace('/\\\\/', DIRECTORY_SEPARATOR, $namespace).'/'.$name.'.php';
    // Storage::mkdirs(dirname($output));
    // $builder->export(DTA_TPL.'/archive.tpl', $output);
    $sql=$builder->getCreateSQL();
    file_put_contents($outsql, "\r\n".$sql."\r\n\r\n", FILE_APPEND);
}
