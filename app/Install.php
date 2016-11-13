<?php
use Core\Caller;

class Install
{
    public function start()
    {
        if (conf('Install_Lock')) {
            self::installLock();
        } else {
            self::installSite();
        }
    }

    public function installLock()
    {
        $time=file_get_contents(DOC_ROOT.'/'.INSTALL_LOCK);
        echo 'Install Locked : Last Install At  '.date('Y-m-d H:i:s', $time);
    }

    public function lock()
    {
        file_put_contents(DOC_ROOT.'/'.INSTALL_LOCK, time());
    }
    public function progress()
    {
        $indb=new Caller('@'.APP_RES.'/install.php');
        $in->call();
        $ret=self::createAdmin(Request::get()->user,Request::get()->passwd);
        if ($ret>0) {
            print $ok.'Create Admin User '.Request::get()->user.', Password is '.Request::get()->passwd."\r\n";
        }
    }
    public function installSite()
    {
        if (Request::hasPost()) {
            $conf=parse_ini_file(DOC_ROOT.'/.conf.simple', true);
            $conf['DEBUG']=0;
            $conf['NoCache']=0;
            $conf['Database']['host']=Request::post()->dbhost('127.0.0.1');
            $conf['Database']['dbname']=Request::post()->dbname('127.0.0.1');
            $conf['Database']['passwd']=Request::post()->dbpass('127.0.0.1');
            $conf['Database']['user']=Request::post()->dbuser('127.0.0.1');
            $conf['Database']['prefix']=Request::post()->dbfix('127.0.0.1');
            Page::set('user', Request::post()->admin);
            Page::set('passwd', Request::post()->passwd);
            Page::use('install-progress');
        } else {
            $success='√';
            $error='×';
            Page::set('res', Storage::isWritable(APP_RES)?$success:$error);
            Page::set('image', function_exists('gd_info')?$success:$error);
            Page::use('install');
        }
    }
    public function createAdmin(string $user, string $passwd):int
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
}
