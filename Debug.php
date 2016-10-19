<?php

require_once 'core/XCore.php';

var_dump(Database::export(APP_RES.'/debug-datebase.php'));
var_dump(Database::exportSQL(APP_RES.'/debug-datebase.sql'));