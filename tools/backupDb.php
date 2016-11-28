<?php
require_once __DIR__.'/../system/initailze.php';
Database::export(SITE_TEMP.'/backupdb.php');
Database::exportSQL(SITE_TEMP.'/backupdb.sql');