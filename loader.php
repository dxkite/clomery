<?php

    defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/core');

    
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    defined('APP_LIB') or define('APP_LIB', DOC_ROOT.'/lib');
    defined('APP_VIEWS')or define('APP_VIEWS',DOC_ROOT.'/views');
   

    defined('APP_INI') or define('APP_INI', '.app.ini');

    
    
    require_once CORE_PATH.'/Core.php';

    $caller=new Core\Caller(['View', 'compile']);
    $caller->call(['pomelo.html', 'pomelo.php']);
    Env::Options('Hello', '<h1>World</h1>')->hello();
    Env::include('Hello', '<h1>World</h1>')->render();