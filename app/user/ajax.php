<?php
namespace user;
use Page;
use Request;

class ajax{
    function main()
    {
        Page::getController()->json();
        return ['name'=>'hello','value'=>Request::json()];
    }
}
    
    