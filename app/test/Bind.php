<?php
namespace test;
class Bind{
    function  __construct(){
        print_r(func_get_args());
        var_dump(base64_encode('DX'.time()));
    }
    function  test(){
        print_r(func_get_args());
        var_dump(base64_encode('DX'.time()));
    }
}