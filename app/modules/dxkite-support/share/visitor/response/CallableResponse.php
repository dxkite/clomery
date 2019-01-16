<?php
namespace dxkite\support\visitor\response;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use dxkite\support\file\File;
use suda\exception\JSONException;
use dxkite\support\visitor\Context;
use dxkite\support\visitor\Permission;
use suda\core\Exception as SudaException;
use dxkite\support\proxy\exception\ProxyException;
use dxkite\support\visitor\response\CallableParamer;
use dxkite\support\visitor\exception\CallableException;
use dxkite\support\visitor\exception\PermissionExcepiton;

abstract class CallableResponse extends MethodCallResponse
{
    protected $defaultParams=[MethodCallResponse::PARAM_JSON, MethodCallResponse::PARAM_POST];
    protected $isrpc=false;
    protected $isJsonp=false;
    protected static $errorHandler =null;
    
    public function onVisit(Context $context)
    {
        // 错误捕获
        self::$errorHandler = [$this,'displayError'];
        hook()->listen('suda:system:exception::display', self::$errorHandler);
        $this->isJsonp= request()->get('jsonp_callback', false);
        try {
            return parent::onVisit($context);
        } catch (JSONException $e) {
            $this->state(500);
            return self::buildResponse($this->error("ParseError", $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            $this->state(500);
            return self::buildResponse($this->error(get_class($e), $e->getCode(), $e->getMessage()));
        }
    }

    public function buildResponse($responseObject)
    {
        if (\is_array($responseObject)) {
            $this->returnJson($responseObject);
        } else {
            return parent::buildResponse($responseObject);
        }
    }

    public static function displayError($e)
    {
        hook()->remove('suda:system:exception::display', self::$errorHandler);
        if (!$e instanceof SudaException) {
            $e=new SudaException($e);
        }
        debug()->writeException($e);
        $responseObject = self::$errorHandler[0] ->error($e->getName(), $e->getCode(), $e->getMessage());
        self::$errorHandler[0]->buildResponse($responseObject);
        return true;
    }

    public function __default()
    {
        if (request()->isJson() && !request()->isGet()) {
            $json=request()->json();
            if ($this->isAssocArray($json)) {
                try {
                    return $this->jsonCall($json);
                } catch (CallableException $e) {
                    return $e->toArray();
                }
            } else {
                $result=[];
                foreach ($json as $call) {
                    try {
                        $result[]=$this->jsonCall($call);
                    } catch (CallableException $e) {
                        $result[]=$e->toArray();
                    }
                }
                return $result;
            }
        } else {
            return $this->getHelpJson();
        }
    }

    protected function getHelpJson()
    {
        $methods=$this->getExportMethods();
        $help=[];
        foreach ($methods as $name=> $methodConfig) {
            $method=$this->buildMethod($methodConfig['callback']);
            $docs= $methodConfig['comment'] ?? $method->getDocComment();
            $help[$name]=[];
            $returnDoc = null;
            $paramDocs = [];
            if ($docs) {
                list($description, $paramDocs, $returnDoc, $data) =self::getDoc($docs);
                $help[$name]['description'] = $description;
                if (preg_match('/@ACL/i', $docs, $match)) {
                    $help[$name]['permissions']= Permission::createFromFunction($method);
                }
                $help[$name]['parameter-from']=$this->getParameterFrom($docs);
            }
            foreach ($method->getParameters() as $param) {
                if (\array_key_exists($param->getName(), $paramDocs)) {
                    $help[$name]['parameters'][$param->getName()]['description'] = $paramDocs[$param->getName()]['description'];
                }
                $help[$name]['parameters'][$param->getName()]['position']=$param->getPosition();
                if ($param->hasType()) {
                    $help[$name]['parameters'][$param->getName()]['type']=$param->getType()->__toString();
                }
                if ($param->isDefaultValueAvailable()) {
                    $help[$name]['parameters'][$param->getName()]['default']=$param->getDefaultValue();
                }
                if ($param->allowsNull()) {
                    $help[$name]['parameters'][$param->getName()]['nullable']=true;
                }
            }
            $help[$name]['return'] = $returnDoc;
        }
        return $help;
    }

    protected static function getDoc(string $docs)
    {
        $docs= trim(preg_replace('/^\/\*\*(.+?)\*\//ms', '$1', $docs));
        $lines=preg_split('/\r?\n/', $docs);
        $params=[];
        $return=[];
        $docs=[];
        foreach ($lines as $index=> $line) {
            $line= substr(ltrim(trim($line), '*'), 1)??' ';
            if (preg_match('/^@param\s+(.+?)\s+(.+?)(\s+(.+))?$/', $line, $match)) {
                if (!isset($match[3])) {
                    $match[3]=null;
                }
                list($comment, $type, $name, $description) = $match;
                $name=ltrim($name, '$');
                $params[$name]['description']=trim($description);
                $params[$name]['type']=$type;
            } elseif (preg_match('/^@return\s+(.+?)(\s+(.+))?$/', $line, $match)) {
                if (!isset($match[2])) {
                    $match[2]=null;
                }
                list($comment, $type, $description) = $match;
                $return['type']=$type;
                $return['description']=trim($description);
            } else {
                $docs[]=$line;
            }
        }
        $datas=static::docField($docs);
        return [$datas['description'],$params,$return,$datas];
    }

    protected static function docField(array $lines)
    {
        $field='document';
        $datas=[
            'description'=>array_shift($lines)
        ];
        foreach ($lines as $line) {
            if (preg_match('/^@(\w+?)(\s+)?$/', $line, $match)) {
                list($line, $field)=$match;
            } else {
                $datas[$field][] = $line;
            }
        }
        foreach ($datas as $name=> $content) {
            if (is_array($content)) {
                $datas[$name]=implode("\r\n", $content);
            }
        }
        return $datas;
    }

    protected function jsonCall(array $jsonData)
    {
        if (self::checkJsonProperty($jsonData)) {
            if (\array_key_exists($jsonData['method'], $this->export)) {
                $callback = $this->export[$jsonData['method']]['callback'];
                $object = self::buildObject($callback);
                $method = self::buildMethod($callback);
                $params = self::buildMethodParams($method, $jsonData['params']??[]);
                return self::resultArray($jsonData['id'], self::runMethod($object, $method, $params));
            } else {
                throw (new CallableException("MethodNotFound Method:".$jsonData['method'], -32601))->setName('MethodNotFound');
            }
        } else {
            throw (new CallableException('Invalid Request', -32600))->setName("InvalidRequest");
        }
    }

    protected function checkJsonProperty(array $json)
    {
        return  isset($json['method']) && isset($json['id']) && !is_null($json['id']);
    }

    protected function error(string $name, int $code, string $message, $data=null)
    {
        return [
            'error'=>[
                'name'=>$name,
                'message'=>$message,
                'code'=>$code,
                'data'=>$data,
            ],
            'id'=>null
        ];
    }

    protected function isAssocArray(array $array)
    {
        return !is_numeric(key($array));
    }

    protected function resultArray(int $id, $result)
    {
        return [
            'result'=>$result,
            'id'=>$id
        ];
    }

    protected function returnJson(array $json)
    {
        if ($callback=request()->get('jsonp_callback')) {
            $this->type('js');
            echo $callback.'('.json_encode($json).');';
        } else {
            return $this->json($json);
        }
    }

    public function onDeny(Context $context)
    {
        throw (new CallableException('Permission Deny'))->setName("PermissionDeny");
    }

    protected function jsonParam($request)
    {
        if ($this->isJsonp) {
            if ($request->get('jsonp_call', false)) {
                return json_decode($request->get('jsonp_call'));
            } else {
                return $request()->get();
            }
        }
        return parent::jsonParam($request);
    }

    protected function buildMethodParams($method, array $params)
    {
        if ($this->isAssocArray($params)) {
            return parent::buildMethodParams($method,$params);
        }
        return $params;
    }
}
