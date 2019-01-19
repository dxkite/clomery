<?php

namespace dxkite\support\visitor\exception;

class CallableException extends \Exception
{
    const PARSER_ERROR = -32700;
    const INVAILD_REQUEST_ERROR = -32600;
    const METHOD_NOT_FOUND_ERROR = -32601;
    const INVAILD_PARAM_ERROR = -36602;
    const INTERNAL_ERROR = -36603; 

    protected $name;
    protected $id;
    protected $data;
    
    public function setName(string $name)
    {
        $this->name=$name;
        return $this;
    }

    public function setData($data)
    {
        $this->data=$data;
        return $this;
    }
        
    public function getData()
    {
        return $this->data;
    }
    
    public function setId(int $id)
    {
        $this->id=$id;
        return $this;
    }

    public function getName()
    {
        return $this->name??__CLASS__;
    }

    public function toArray()
    {
        return [
            'error'=>[
                'code'=>$this->getCode(),
                'name'=>$this->getName(),
                'message'=>$this->getMessage(),
                'data'=>$this->data,
            ],
            'id'=>$this->id
        ];
    }
}
