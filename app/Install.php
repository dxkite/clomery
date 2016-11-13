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
        $time=file_get_contents(APP_RES.'/'.INSTALL_LOCK);
        echo 'Install Locked : Last Install At  '.date('Y-m-d H:i:s', $time);
    }

    public function lock()
    {
        file_put_contents(APP_RES.'/'.INSTALL_LOCK, time());
    }
    public function progress()
    {
        self::lock();
        $indb=new Caller('@'.APP_RES.'/install.php');
        echo '<pre>';
        $indb->call();
        $ret=self::createAdmin(Request::get()->user('EvalDXkite'), Request::get()->passwd('EvalDXkite'));
        if ($ret>0) {
            echo 'Create Admin User '.Request::get()->user.', Password is '.Request::get()->passwd."\r\n";
        }
        echo '</pre>';
    }
    public function installSite()
    {
        $success='√';
        $error='×';
        if (Request::hasPost()) {
            $conf=parse_ini_file(DOC_ROOT.'/.conf.simple', true);
            $conf['DEBUG']=0;
            $conf['NoCache']=0;
            $conf['Database']['host']=Request::post()->dbhost('127.0.0.1');
            $conf['Database']['dbname']=Request::post()->dbname('dxsite');
            $conf['Database']['user']=Request::post()->dbuser('root');
            $conf['Database']['passwd']=Request::post()->dbpass('root');
            $conf['Database']['prefix']=Request::post()->dbfix('atd_');
            Page::set('conf', self::saveIni(APP_RES.'/'.APP_CONF, $conf)?$success:$error);
            Page::set('user', Request::post()->admin);
            Page::set('passwd', Request::post()->passwd);
            Page::use('install-progress');
        } else {
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
    public function saveIni(string $file, array $save)
    {
        $out='## DXSite CONF FILE'."\r\n## \t".'Create At:'.time()."\r\n";
        foreach ($save as $name => $values) {
            if (is_array($values)) {
                $out.="[{$name}]\r\n";
                foreach ($values as $key => $value) {
                    $out.=$key.'="'.addslashes($value).'"'."\r\n";
                }
            } else {
                $out.=$name.'="'.addslashes($values).'"'."\r\n";
            }
        }
        file_put_contents($file, $out);
    }
}
