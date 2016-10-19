<?php

class Install_EnvCheck
{
    public $sysinfo=[];
    public function check(){
        
        $sysinfo['Resource_Writeable']=Storage::isWriteable(APP_RES);

    }
}
