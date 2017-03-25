<?php

namespace cn\atd3;

class ApiException extends \Exception implements \JsonSerializable
{
    protected $name,$msg;
    public function __construct(string $name, string $message)
    {
        $this->name=$name;
        $this->msg=$message;
        parent::__construct($name.':'.$message);
    }
    public function jsonSerialize()
    {
        return ['error'=>$this->name, 'message'=>$this->msg];
    }
}
