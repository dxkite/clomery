<?php

    defined('DOC_ROOT') or define('DOC_ROOT', dirname(__DIR__));
    defined('WEB_ROOT') or define('WEB_ROOT', DOC_ROOT.'/public');
    defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/core');

    
    defined('APP_ROOT') or define('APP_ROOT', DOC_ROOT.'/app');
    defined('APP_LIB') or define('APP_LIB', APP_ROOT.'/library');
    defined('APP_VIEWS')or define('APP_VIEWS',APP_ROOT.'/views');
   
    defined('MINI_ROOT') or define('MINI_ROOT', DOC_ROOT.'/mini');
    defined('MINI_INI') or define('MINI_INI', '.mini.ini');
    defined('MINI_LIB') or define('MINI_LIB', __DIR__.'/library');
    
    
    require_once CORE_PATH.'/Core.php';

    $caller=new Core\Caller(['View', 'test']);
    $caller->call(['pomelo.html', 'pomelo.php']);
    Env::Options('Hello', '<h1>World</h1>')->hello();
    Env::include('Hello', '<h1>World</h1>')->render();