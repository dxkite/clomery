<?php
namespace user;
use Page;
class ajax{
    function main()
    {
        Page::getController()->json();
        echo 'hello';
        return ['name'=>'hello'];
    }
}
    
    