<?php

namespace cn\atd3\visitor\response;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use Exception;
use cn\atd3\visitor\Context;
use cn\atd3\upload\File;
use cn\atd3\visitor\Permission;

/**
 * 自动调用函数接口来响应
 */
abstract class MethodCallResponse extends Response
{
    // 参数生成
    const PARAM_GET=1;
    const PARAM_POST=2;
    const PARAM_JSON=3;
    const PARAM_FILES=4;

    protected $export;
    protected $request;

    // 默认调用接口
    protected $defaultMethod='__default';
    // 默认函数调用参数来源
    protected $defaultParams=[MethodCallResponse::PARAM_GET];
    // 默认接口开放状态
    protected $defaultOpen=true;

    public function onVisit(Context $context)
    {
        $this->export=$this->getExportMethods();
        $request=$context->getRequest();
        $this->request=$request;
        $method=$request->get()->method($this->defaultMethod);
        $param_arr=[];
        if (isset($this->export[$method]['comment']) && preg_match('/@paramSource\s+([\w,]+)\s*$/ims', $this->export[$method]['comment'], $match)) {
            $types=explode(',', strtoupper(trim($match[1], ',')));
            $alias=[
                    'GET'=>MethodCallResponse::PARAM_GET,
                    'POST'=>MethodCallResponse::PARAM_POST,
                    'JSON'=>MethodCallResponse::PARAM_JSON,
                    'FILES'=>MethodCallResponse::PARAM_FILES,
                ];
            
            foreach ($types as $type) {
                $param_arr=array_merge($param_arr, $this->getParams($alias[$type]));
            }
        } else {
            foreach ($this->defaultParams as $type) {
                $param_arr=array_merge($param_arr, $this->getParams($type));
            }
        }
        if (isset($this->export[$method])) {
            return $this->runMethod($this->export[$method]['callback'], $param_arr);
        } else {
            return $this->runMethod([$this, $this->defaultMethod], $param_arr);
        }
    }

    abstract public function __default();
    
    /**
     * 获取导出的接口
     *
     * @return void
     */
    public function getExportMethods($enter=null)
    {
        $enterclass=is_null($enter)?$this:$enter;
        $methods=[];
        if (is_array($enterclass)) {
            while ($class=array_pop($enterclass)) {
                if (is_object($class)) {
                    $classInstance=$class;
                } else {
                    $class=class_name($class);
                    $classInstance=new $class(Context::getInstance());
                }
                $methodNew=$this->getExportMethodFromClass($classInstance);
                $methods=array_merge($methods, $methodNew);
            }
        }else{
            $methods=$this->getExportMethodFromClass($classInstance);
        }
        return $methods;
    }

    protected function getExportMethodFromClass($enterclass)
    {
        $class=new ReflectionClass($enterclass);
        $export=array();
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name=$method->getShortName();
            if ($method->getDeclaringClass()->getName()===get_class($enterclass) && !preg_match('/^__/', $name)) {
                // 如果有DOCS
                if ($docs=$method->getDocComment()) {
                    // 设置了是否开放
                    if (preg_match('/@open\s+(\w+)\s+/ims', $docs, $match)) {
                        // 是否开放端口
                        if (!filter_var(strtolower($match[1]??'true'), FILTER_VALIDATE_BOOLEAN)) {
                            continue; // 不开放则跳过
                        }
                    } elseif (!$this->defaultOpen) {
                        continue; // 不设置则跳过
                    }
                    $export[$name]['comment']=$docs;
                } elseif (!$this->defaultOpen) {
                    continue; // 不设置则跳过
                }
                $export[$name]['callback']=[$enterclass,$name];
            }
        }
        return $export;
    }

    public function runMethod($method, array $param_arr)
    {
        if ($method instanceof ReflectionMethod) {
            if ($method->getDeclaringClass()===get_class($this)) {
                $object=$this;
            } else {
                $class=$method->getDeclaringClass();
                $object=$class->newInstance();
            }
            if ($method->getDocComment()) {
                if ($this->getContext()->getVisitor()->canAccess($method)) {
                    return $method->invokeArgs($object, $param_arr);
                } else {
                    return $this->onDeny($this->getContext());
                }
            } else {
                return $method->invokeArgs($object, $param_arr);
            }
        } elseif (count($method)>1) {
            if (is_string($method[0]) && $method[0]===get_class($this)) {
                $object=$this;
            } elseif (is_object($method[0])) {
                $object=$method[0];
            } else {
                $object=(new ReflectionClass($method[0]))->newInstance();
            }
            $method=new ReflectionMethod($method[0], $method[1]);

            if ($method->getDocComment()) {
                if ($this->getContext()->getVisitor()->canAccess($method)) {
                    return $method->invokeArgs($object, $param_arr);
                } else {
                    return $this->onDeny($this->getContext());
                }
            } else {
                return $method->invokeArgs($object, $param_arr);
            }
        } else {
            if (!$method instanceof ReflectionFunction) {
                $method=new ReflectionFunction($method);
            }
            if ($method->getDocComment()) {
                if ($this->getContext()->getVisitor()->canAccess($method)) {
                    return $method->invokeArgs($param_arr);
                } else {
                    return $this->onDeny($this->getContext());
                }
            } else {
                return $method->invokeArgs($param_arr);
            }
        }
    }
    
    //TODO :add paramsCheck


    public function getParams(int $type=MethodCallResponse::PARAM_GET):array
    {
        switch ($type) {
            case MethodCallResponse::PARAM_GET:
                return $this->request->get()->_getVar();
            case MethodCallResponse::PARAM_POST:
                return $this->request->post()->_getVar();
            case MethodCallResponse::PARAM_JSON:
                return $this->jsonParam($this->request);
            case MethodCallResponse::PARAM_FILES:
                return $this->request->files()->_getVar();
            default:
                return array_merge($this->request->get()->_getVar(), $this->request->post()->_getVar(), $this->request->json());
        }
    }

    protected function jsonParam($request)
    {
        $is_fromdata=preg_match('/multipart\/form-data/i', $_SERVER['CONTENT_TYPE']??'');
        if ($request->isPost() && $is_fromdata) {
            $params=$this->request->post()->_getVar();
            $files=$request->files()->_getVar();
            foreach ($files as $name=> $file) {
                $params[$name]=File::createFromPost($name);
            }
            return $params;
        }
        return $this->request->json()??[];
    }
}
