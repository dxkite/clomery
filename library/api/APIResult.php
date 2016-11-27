<?php
namespace api;

class APIResult extends \helper\Value
{
    public function __construct($result, $message)
    {
        parent::__construct(['result'=>$result, 'value'=>$message]);
    }
}