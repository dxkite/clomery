<?php
    
    defined ('DOC_ROOT') or define ('DOC_ROOT',dirname(__DIR__));
    defined ('MINI_ROOT') or define ('MINI_ROOT',DOC_ROOT.'/mini');
    defined ('APP_PATH') or define ('APP_PATH',DOC_ROOT.'/app');
    defined ('WEB_ROOT') or define ('WEB_ROOT',DOC_ROOT.'/public');
    defined ('MINI_INI') or define ('MINI_INI','.mini.ini');
    defined ('MINI_LIB') or define ('MINI_LIB',__DIR__.'/library');
    defined ('CORE_PATH') or define ('CORE_PATH',__DIR__.'/core');
    
    $ini=parse_ini_file('D:\Server\three\three\.mini.ini',true);
    require_once CORE_PATH.'/Core.php';

    $caller=new Core\Caller(['View','test']);
    $caller->call(['pomelo.html','pomelo.php']);