<?php
/** Debug Install Script **/
// TODO:自增值归0
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__ .'/..');
require_once '../core/XCore.php';
$ok="\033[32m[Ok]\033[0m ";
$info="\033[33m[Info]\033[0m ";
$failed="\033[31m[failed]\033[0m ";
function createAdmin(string $user, string $passwd):int
{
    if (($q=new Query('INSERT INTO #{users} (`uname`,`upass`,`signup`,`gid`) VALUES ( :uname, :passwd, :signup ,:gid );'))->values([
            'uname'=>$user,
            'passwd'=>password_hash($passwd, PASSWORD_DEFAULT),
            'signup'=>time(),
            'gid'=>1,
        ])->exec()) {
        $uid=$q->lastInsertId();
        Common_User::setDefaulInfo($uid, 0, 'Ta很懒，神马都没留下');
        return $uid;
    }
    return 0;
}
Storage::mkdirs(APP_RECYCLE_BIN);
$time=date('Y_m_d_H_i_s');
Database::export(APP_RECYCLE_BIN.'/datebase_'.$time.'.php');
Database::exportSQL(APP_RECYCLE_BIN.'/datebase_'.$time.'.sql');

print $info.'Save Old Database To Recycle Bin >> datebase_'.$time.".*\r\n";
if (!Storage::exist(APP_RES.'/'.APP_CONF)) {
    print $info.'Please Modify '.DOC_ROOT.'/.conf.simple Configurtion And Save To '.APP_RES.'/'.APP_CONF."\r\n";
    exit(-1);
}

if (system('chmod -R a+rw '.APP_RES)) {
    print $ok.' Change Permition  To a+rw '."\r\n";
} else {
    print $info.' Permition Change Faild,Pelase Makesure Apache Can Use '.APP_RES."\r\n";
}

if (Storage::exist(APP_RES.'/datebase.php')) {
    if (Database::import(APP_RES.'/datebase.php')) {
        print $ok.' Installed Database'."\r\n";
    } else {
        print $failed. 'Import Database Failed! Please Import Date Use '.APP_RES.'/datebase.sql'."\r\n";
        exit(-3);
    }
} else {
    print $failed.'Database File('.APP_RES.'/datebase.php) Do Not Exist,Please Make sure the Source Code Is Avaliable'."\r\n";
    exit(-3);
}
$ret=createAdmin('EvalDXkite','EvalDXkite');
if ($ret>0) {
    print $ok.'Create Admin User EvalDXkite, Password is EvalDXkite'."\r\n";
}
print $ok.'Install Debug Release Ok, Enjoy It!'."\r\n";
