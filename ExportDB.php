<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
require_once 'core/XCore.php';

echo 'export Database:'.APP_RES.'/datebase.php '.Database::export(APP_RES.'/datebase.php',['nav','site_options','permission','category'])."\r\n";
echo 'export SQL Database:'.APP_RES.'/datebase.sql '.Database::exportSQL(APP_RES.'/datebase.sql',['nav','site_options','permission','category'])."\r\n";

$command=[];
$set=null;
/**
 -k keep value 保存值的数据库
 -c create not exist 
 -r refrech 
 -i ignore 
**/
/*
foreach ($_SERVER['argv'] as $input) {
    if (preg_match('/^-(\w)/', $input, $match)) {
        $command[]=$match[0];
        $set=$match[1];
    } elseif ($set) {
        $command[$set]=$input;
    } else {
        $command[]=$input;
    }
}
var_dump($command);
*/