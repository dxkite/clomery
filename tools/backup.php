<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__ .'/..');
require_once __DIR__.'/../core/XCore.php';
Database::export(APP_TMP.'/backupdb.php');
Database::exportSQL(APP_TMP.'/backupdb.sql');