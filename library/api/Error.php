<?php
namespace api;
class Error extends \helper\Value
{
    function __construct(string $name,stirng $message){
        parent::__construct(['name'=> $name ,'message'=>$message]);
    }
}