<?php

namespace dxkite\support\visitor\response;

use ReflectionMethod;
use dxkite\support\visitor\exception\RPCException;
use Exception;
use ReflectionClass;
use ReflectionFunction;
use dxkite\support\visitor\Context;
use dxkite\support\exception\JSONException;

abstract class PRCCallResponse extends CallableResponse
{
    protected $defaultParams=[MethodCallResponse::PARAM_JSON];

    protected function resultArray(int $id, $result)
    {
        return [
            'jsonrpc'=>'2.0',
            'result'=>$result,
            'id'=>$id
        ];
    }

    public function __default()
    {
        return $this->getHelpJson();
    }
    
    protected function checkJsonProperty(array $json)
    {
        return  isset($json['method']) && $json['jsonrpc'] =='2.0' && isset($json['id']) && !is_null($json['id']);
    }

    protected function returnJson(array $json)
    {
        $json['jsonrpc'] = '2.0';
        return $this->json($json);
    }
}
