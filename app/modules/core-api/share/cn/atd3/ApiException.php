<?php

namespace cn\atd3;

class ApiException extends \Exception implements \JsonSerializable
{
    protected $name,$msg,$data;
    public function __construct(string $name, string $message,$data=null)
    {
        $this->name=$name;
        $this->msg=$message;
        $this->data=$data;
        parent::__construct($name.':'.$message);
    }
    public function jsonSerialize()
    {
        return ['error'=>$this->name, 'message'=>$this->msg,'data'=>$this->data];
    }
}
