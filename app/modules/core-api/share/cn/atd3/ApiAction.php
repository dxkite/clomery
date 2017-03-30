<?php
namespace cn\atd3;

use ReflectionClass;
use ReflectionMethod;
use suda\tool\Value;

class ApiAction extends ApiResponse
{
    public function action(string $action, Value $data)
    {
        if (method_exists($this, $method='action'.ucfirst($action))) {
            $method=new ReflectionMethod($this, $method);
            $args=[];
            // 压入调用参数
            foreach ($method->getParameters() as $param) {
                $name=$param->getName();
                $pos=$param->getPosition();
                if (isset($data->$name)) {
                    if ($param->hasType()) {
                        $val=$data->$name;
                        if (@settype($val, $param->getType()->__toString())) {
                            $args[$pos]=$val;
                        } else {
                            return $this->data('paramTypeCastException', _T('参数 %s 无法转化成 %s 类型！',$name,$param->getType()->__toString()) );
                        }
                    } else {
                        $args[$pos]=$data->$name;
                    }
                } elseif (!$param->isDefaultValueAvailable()) {
                    return $this->data(['name'=>$name, 'pos'=>$pos], 'paramError', _T('参数错误，需要参数: %s',$name));
                }
            }
            // 检查权限
            $docs=$method->getDocComment();
            if ($docs && preg_match('/@auths(?:\s*:\s*([\w,]+))?\s*$/im',$docs,$match)){
                $auths=null;
                if (isset($match[1])){
                    $auths=explode(',',trim($match[1],','));
                }
                $this->check($auths);
            }
            // 调用接口
            return $this->data($method->invokeArgs($this, $args));
        } else {
            $this->printHelp();
        }
    }

    public function printHelp()
    {
        $class=new ReflectionClass($this);
        $help=[];
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name=$method->getShortName();
            if (preg_match('/^action(.+?)$/', $name, $match)) {
                $name=lcfirst($match[1]??$name);
                $help[$name]['doc']=$method->getDocComment();
                foreach ($method->getParameters() as $param) {
                    $help[$name]['parameters'][$param->getName()]['pos']=$param->getPosition();
                    if ($param->hasType()) {
                        $help[$name]['parameters'][$param->getName()]['type']=$param->getType()->__toString();
                    }
                    if ($param->isDefaultValueAvailable()) {
                        $help[$name]['parameters'][$param->getName()]['default']=$param->getDefaultValue();
                    }
                }
            }
        }
        return $this->json(['doc'=>$class->getDocComment(),'methods'=>$help]);
    }
}
