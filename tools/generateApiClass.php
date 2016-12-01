<?php

require_once __DIR__.'/../system/initailze.php';

function generateApiClass(string $name, string $interface, string $namespace, string $comment, string $permission='')
{
    $pathnam=preg_replace_callback('/[A-Z]/', function ($match) {
        return '_'.strtolower($match[0]);
    }, $name);
    $path=SITE_TEMP.'/api/'.str_replace('\\', '/', $namespace).'/'.strtolower($pathnam).'.post.php';
    $path=preg_replace('/\\\\+|\/+/', DIRECTORY_SEPARATOR, $path);
    ob_start();
    include __DIR__ .'/tpl/json.php';
    $content=ob_get_clean();
    Storage::mkdirs(dirname($path));
    echo $path."\r\n";
    file_put_contents($path, "<?php\r\n".$content);
}

function generateClass(string $class_name)
{
    $class=new ReflectionClass($class_name);
    foreach ($class->getMethods() as $method) {
        $param_list=[];
        foreach ($method->getParameters() as $parameter) {
            $name=$parameter->getName();
            if ($parameter->hasType()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $value=[$parameter->getType().'',$parameter->getDefaultValue()];
                } else {
                    $value=$parameter->getType().'';
                }
                $param_list[$name]=$value;
            } else {
                $param_list[]=$name;
            }
        }
        
        $param = var_export($param_list, true);
        $callback=$method->class.'::'.$method->name;
        $tpl="return api_check_callback(\$param,$param,'$callback');";

        if ($comment=$method->getDocComment()) {
            preg_match('/@Auth\s*?:\s*?(\w+)\s*?/i', $comment, $match);
            $auth=isset($match[1])?$match[1]:'';
        } else {
            $auth='';
        }
        $namespace=preg_replace('/\\\\+/', '\\', '\\'.str_replace('model', '', $class->getName()));
        $namespace=$namespace==='\\'?'':$namespace;
        generateApiClass($method->getName(), $tpl, strtolower($namespace), $comment, $auth);
    }
}

generateClass($argv[1]);
