<?php
require_once __DIR__.'/../system/initailze.php';

Storage::mkdirs(SITE_TEMP.'/database');
$time=date('Y_m_d_H_i_s');
Database::export($bkphp=SITE_TEMP.'/database/datebase_'.$time.'.php');
Database::exportSQL($bksql=SITE_TEMP.'/database/datebase_'.$time.'.sql');
$php=Storage::get($bkphp);
$php=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$php);
Storage::put(SITE_RESOURCE.'/install.php',$php);
$sql=Storage::get($bksql);
$sql=preg_replace('/AUTO_INCREMENT=\d+/','AUTO_INCREMENT=0',$sql);
Storage::put(SITE_RESOURCE.'/install.sql',$sql);
echo 'created install database file';
