<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
require_once 'core/XCore.php';
echo 'export Database:'.APP_RES.'/datebase.php '.Database::export(APP_RES.'/datebase.php',['nav','site_options'])."\r\n";
echo 'export SQL Database:'.APP_RES.'/datebase.sql '.Database::exportSQL(APP_RES.'/datebase.sql',['nav','site_options'])."\r\n";