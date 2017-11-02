<?php
namespace cn\atd3\proxy;

use cn\atd3\proxy\Proxy;
use suda\template\compiler\suda\Compiler;

require_once __DIR__ .'/functions.php';

class ProxyCall
{
    const ROW=0;
    const ROWS=1;
    public static function addProxyCall(Compiler $compiler)
    {
        // 添加DAO
        $compiler->addCommand('call', function ($exp) {
            if ($exp) {
                $params=\cn\atd3\view\ProxyCall::param($exp);
                $type=strtoupper($params['type']??'rows');
                $method=\cn\atd3\view\ProxyCall::echoValue($params['method']);
                $class=trim($params['class'], '"\'');
                $null=trim($params['null']??'this is null', '"\'');
                return '<?php cn\atd3\view\ProxyCall::call(\''.$class.'\',$this,function($class,$page,$type){
    $fields=$class->'.$method.'; 
    $type=cn\atd3\view\ProxyCall::'.$type.';
    $null=\''. $null .'\';
    $callback=function(array $field) { extract($field); ?>';
            } else {
                return '<?php };
    if ($fields){
        if ($type===cn\atd3\view\ProxyCall::ROWS){
            foreach ($fields as $field){
                $callback($field);
            }
        }
        else{
            $callback($fields);
        }     
    } else {
        echo $null; 
    }
});?>';
            }
        });
    }
    public static function param(string $exp)
    {
        $exp=preg_replace('/\((.+)\)/', '$1', $exp);
        $exp=trim(trim($exp), ';');
        $sets=explode(';', $exp);
        $values=[];
        foreach ($sets as $str) {
            list($key, $value)=explode(':', $str, 2);
            $values[trim($key)]=trim($value);
        }
        return $values;
    }
    
    public static function echoValue($var)
    {
        return preg_replace_callback('/\B[$][:]([.\w\x{4e00}-\x{9aff}]+)(\s*)(\( ( (?>[^()]+) | (?3) )* \) )?/ux', function ($matchs) {
            $name=$matchs[1];
            $args=isset($matchs[4])?','.$matchs[4]:'';
            return '$this->get("'.$name.'"'.$args.')';
        }, $var);
    }

    public static function call(string $proxyname, $template, $callback)
    {
        $class=new Proxy(new $proxyname($template->getResponse()->getContext()));
        $callback($class, $template, $callback);
    }
}
