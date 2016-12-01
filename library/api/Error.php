<?php
namespace api;
class Error extends \helper\Value
{
    function __construct(string $name,string $message){
        parent::__construct(['error'=>['name'=> $name ,'message'=>$message]]);
    }
}