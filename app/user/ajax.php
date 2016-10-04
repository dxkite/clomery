<?php
namespace user;
use Page;
class ajax{
    function main()
    {
        Page::getController()->json();
        return ['name'=>'hello'];
    }
}
    
    