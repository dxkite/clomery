<?php
defined('DOC_ROOT') or define('DOC_ROOT', __DIR__);
require_once 'core/XCore.php';

$params=array_slice($argv, 2);
if (isset($argv[1])) {
    print "\033[36mcall \033[34m".$argv[1]."(\033[32m".implode(',', $params)."\033[34m)\033[0m\r\n";
    print "\033[33m-----------------------------------\033[0m\r\n";
    ob_start();
    $return=(new Core\Caller($argv[1]))->call($params);
    $output=ob_get_clean();
    if ($output) {
        print "\033[33m# Function echo\033[0m\r\n";
        print "\033[33m-----------------------------------\033[0m\r\n";
        echo $output;
        print "\r\n\r\n";
    }
    
    print "\033[33m# return value\033[0m\r\n";
    print "\033[33m-----------------------------------\033[0m\r\n";
    print_r($return);
} else {
    print "\033[31mplease enter caller string\r\n\033[0m";
}
