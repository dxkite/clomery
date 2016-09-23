<?php
use Core\Arr; // 引入Arr数组操纵类

spl_autoload_register(function ($name) {
    static $imported=[];
    if (isset($imported[$name])) {
        return $imported[$name];
    }
    $paths=[APP_LIB, CORE_PATH, APP_ROOT]; // 搜索目录
    $name=preg_replace('/[\\_]/', DIRECTORY_SEPARATOR, $name);
    foreach ($paths as $root) {
        // 优先查找文件
        if (file_exists($require=$root.'/'.$name.'.php')) {
            $imported[$name]=$require;
            require_once $require;
        }
        // 其次查找目录配驱动
        elseif (is_dir($root.'/'.$name)) {
            // 配置存在
            if (conf('Driver.'.$name) && file_exists($require="{$root}/{$name}/".conf('Driver.'.$name)."_{$name}.php")) {
                require_once $require;
            }
        }
    }
});

// 获取配置
/**
获取conf 
*/
function conf(string $name, $default=null)
{
    static $conf=null;
    if (is_null($conf)) {
        $conf=parse_ini_file(DOC_ROOT.'/'.APP_CONF, true);
    }
    return Arr::get($conf, $name, $default);
}
// 获取MIME
function mime(string $name=null, $default=null)
{
    static $mime=null;
    if (is_null($mime)) {
        $mime=parse_ini_file(DOC_ROOT.'/'.WEB_MIME);
    }
    if (is_null($name)) return $mime;
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


