<?php
namespace api;
class Success extends \helper\Value
{
    function __construct($message){
        parent::__construct(['name'=>'success','detail'=>$message]);
    }
}