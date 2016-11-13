<?php
/** Debug Install Script **/

defined('DOC_ROOT') or define('DOC_ROOT', __DIR__ .'/..');
require_once __DIR__.'/../core/XCore.php';
$ok="\033[32m[Ok]\033[0m ";
$notice ="\033[33m[Notice]\033[0m ";
$failed="\033[31m[failed]\033[0m ";

if (!Storage::exist(APP_RES.'/'.APP_CONF)) {
    print $notice.'Please Modify '.DOC_ROOT.'/.conf.simple Configurtion And Save To '.APP_RES.'/'.APP_CONF."\r\n";
    exit(-1);
}

Storage::mkdirs(APP_RECYCLE_BIN);

$time=date('Y_m_d_H_i_s');
Database::export(APP_RECYCLE_BIN.'/datebase_'.$time.'.php');
Database::exportSQL(APP_RECYCLE_BIN.'/datebase_'.$time.'.sql');
print $notice.'Save Old Database To Recycle Bin >> datebase_'.$time.".*\r\n";
if (system('chmod -R a+rw '.APP_RES)) {
    print $notice.' Permition Change Faild,Pelase Makesure Apache Can Use '.APP_RES."\r\n";
} else {
    print $ok.' Change Resource Dir:"'.APP_RES.'"\'s Permition  To a+rw '."\r\n";
}

if (Storage::exist(APP_RES.'/install.php')) {
    if (Database::import(APP_RES.'/install.php')) {
        print $ok.' Installed Database'."\r\n";
    } else {
        print $failed. 'Import Database Failed! Please Import Date Use '.APP_RES.'/install.sql'."\r\n";
        exit(-3);
    }
} else {
    print $failed.'Database File('.APP_RES.'/install.php) Do Not Exist,Please Make sure the Source Code Is Avaliable'."\r\n";
    exit(-3);
}
$ret=app\Install::createAdmin('EvalDXkite', 'EvalDXkite');
if ($ret>0) {
    print $ok.'Create Admin User EvalDXkite, Password is EvalDXkite'."\r\n";
}
print $ok.'Install Debug Release Ok, Enjoy It!'."\r\n";
