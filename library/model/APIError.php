<?php
namespace model;

class APIError extends \helper\Value
{
    public function __construct($name, $message)
    {
        parent::__construct(['name'=>$name, 'message'=>$message]);
    }
}
