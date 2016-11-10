<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__ .'/..');
require_once __DIR__.'/../core/XCore.php';

Storage::mkdirs(APP_TMP.'/database');
$time=date('Y_m_d_H_i_s');
Database::export($bkphp=APP_TMP.'/database/datebase_'.$time.'.php',['nav','site_options','permission']);
Database::exportSQL($bksql=APP_TMP.'/database/datebase_'.$time.'.sql',['nav','site_options','permission']);
$php=Storage::get($bkphp);
$php=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$php);
Storage::put(APP_RES.'/install.php',$php);
$sql=Storage::get($bksql);
$sql=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$sql);
Storage::put(APP_RES.'/install.sql',$php);
echo 'created install database file';
