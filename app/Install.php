<?php
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
        $msg=['install database','create res','create Administer'];
        foreach ($msg as $touch) {
            sleep(1);
            echo "<div>{$touch}</div>";
            ob_flush();
            flush();
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
}
