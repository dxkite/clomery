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
        echo 'Install Locked : Last Install At  '.date('Y-m-d H:i:s');
    }
    public function installSite()
    {
        echo 'Install....';
    }
    
    public function releaseInstall(){

    }
}
