<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__ .'/..');
require_once '../core/XCore.php';

Storage::mkdirs(APP_BACKUP);
$time=date('Y_m_d_H_i_s');
Database::export(APP_BACKUP.'/datebase_'.$time.'.php');
Database::exportSQL(APP_BACKUP.'/datebase_'.$time.'.sql');
print $info.'BackupDateBase >> datebase_'.$time.".*\r\n";
$php=Storage::get(APP_RES.'/datebase.php');
$php=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$php);
Storage::put(APP_RES.'/install.php',$php);
$sql=Storage::get(APP_RES.'/datebase.sql');
$sql=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$sql);
Storage::put(APP_RES.'/install.sql',$php);
echo 'created install database file';
