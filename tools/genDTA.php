<?php
require_once __DIR__.'/../system/initailze.php';
$params=array_slice($argv, 1);
$src=isset($params[0])?$params[0]:SITE_LIB.'/dta';
$dist=isset($params[1])?$params[1]:SITE_LIB;
print_r(Storage::readDirFiles($src,true,'/\.dta$/',true));