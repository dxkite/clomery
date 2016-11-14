<?php

use Core\Arr; // 引入Arr数组操纵类

spl_autoload_register('import');

function import(string $name)
{
    static $imported=[];
    if (isset($imported[$name])) {
        return $imported[$name];
    }
    // App运行库为独立控制
    if (preg_match('/^app(?:\\\\|_)(.*)$/', $name, $matchs)) {
        $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $matchs[1]);
        $app_loader=APP_ROOT.'/'.$fname.'.php';
        // 优先查找文件
        if (file_exists($app_loader)) {
            require_once $app_loader;
            class_alias($matchs[1], $name);
            $imported[$name]=$app_loader;
            return $app_loader;
        }
    }

    $fname=preg_replace('/[\\\\_\/.]/', DIRECTORY_SEPARATOR, $name);

    $paths=[APP_LIB, CORE_PATH]; // 搜索目录

    foreach ($paths as $root) {
        // 优先查找文件
        if (file_exists($require=$root.'/'.$fname.'.php')) {
            $imported[$name]=$require;
            require_once $require;
            return $require;
        }
        // 其次查找目录配驱动
        elseif (is_dir($dir=$root.'/'.$fname)) {
            $option=strpos($fname, DIRECTORY_SEPARATOR)?substr($fname, 0, strpos($fname, DIRECTORY_SEPARATOR)):$fname;
            $fname=strpos($fname, DIRECTORY_SEPARATOR)?substr($fname, strpos($fname, DIRECTORY_SEPARATOR)+1):$fname;
            // 配置存在
            if (conf('Driver.'. $option) && file_exists($require=$dir.'/'.conf('Driver.'. $option)."_{$fname}.php")) {
                require_once $require;
                return $require;
            }
        }
    }
}

/**
 * 获取conf配置
 * @param string $name
 * @param null $default
 * @return mixed
 */
function conf(string $name, $default=null)
{
    return Configuration::instance()->get($name,$default);
}

/**
 * @param string|null $name
 * @param null $default
 * @return array|mixed|null
 */
function mime(string $name=null, $default=null)
{
    static $mime=null;
    if (is_null($mime)) {
        if (file_exists($path=CORE_PATH.'/'.WEB_MIME)) {
            $mime=parse_ini_file($path);
        } else {
            die('<h1>Missing the mine file ( CORE_PATH/'.WEB_MIME.'), Please ensure the integrity of the program.</h1> <a href="https://github.com/DXkite/MongCix" title="CLONE ME ON THE GITHUB" >CLONE ME ON THE GITHUB</a>');
        }
    }
    if (is_null($name)) {
        return $mime;
    }
    return Arr::get($mime, $name, $default);
}

function send_http_status($code)
{
    static $_status = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded',
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        header('Status:'.$code.' '.$_status[$code]);
    }
}


function is_spider()
{
    $is_spider = false;
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $tmp = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($tmp, 'Googlebot') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Baiduspider') >0) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Yahoo! Slurp') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'msnbot') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Sosospider') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'YodaoBot') !== false || strpos($tmp, 'OutfoxBot') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Sogou web spider') !== false || strpos($tmp, 'Sogou Orion spider') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'fast-webcrawler') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Gaisbot') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'ia_archiver') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'altavista') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'lycos_spider') !== false) {
            $is_spider = true;
        } elseif (strpos($tmp, 'Inktomi slurp') !== false) {
            $is_spider = true;
        }
    } else {
        $is_spider = true;
    }
    
    return $is_spider;
}
