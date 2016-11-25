<?php
require_once __DIR__.'/../system/initailze.php';
defined('DTA_TPL') or define('DTA_TPL',__DIR__ .'/tpl');
use template\Builder as TplBuilder;
use archive\Builder  as Builder;

$params=array_slice($argv, 1);
$src=isset($params[0])?$params[0]:SITE_LIB.'/dta';
$dist=isset($params[1])?$params[1]:SITE_LIB;
$tables=Storage::readDirFiles($src,true,'/\.dta$/',true);

function compileAll(){
    $files=Storage::readDirFiles(DTA_TPL, true, '/\.raw$/');
    foreach ($files as $file){
        $text=(new TplBuilder())->compileText(file_get_contents($file));
        $to=preg_replace('/\.raw/','.tpl',$file);
        file_put_contents($to,$text);
    }
}

compileAll();
foreach ($tables as $table){
    $name=ucfirst(pathinfo($table,PATHINFO_FILENAME));
    $namespace=preg_replace('/\\\\\//','\\',dirname($table));
    $table_name=$name===$namespace?$name:preg_replace('/\\\\/','_',$namespace);
    $builder=new Builder;
    $builder->load($src.'/'.$table);
    $builder->setName($name);
    $builder->setNamespace($namespace);
    $output=$dist.'/'.preg_replace('/\\\\/',DIRECTORY_SEPARATOR,$namespace).'/'.$name.'.php';
    Storage::mkdirs(dirname($dist));
    $builder->export(DTA_TPL.'/archive.tpl',$output);
}
