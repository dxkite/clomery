<?php
namespace model;

class APIError extends \helper\Value
{
    public function __construct($name, $message)
    {
        parent::__construct(['result'=>false,'value'=>['name'=>$name, 'message'=>$message]]);
    }
}
