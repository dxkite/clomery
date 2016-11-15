<?php
namespace test;

use Core\Value;
use Core\ArrayValue;

class testValue
{
    public function testIterator()
    {
        $var =new Value(['name'=>'DXkite', 'id'=>3, 'qq'=>'noqq']);
        var_dump($var);
        foreach ($var  as $key => $value) {
            var_dump($key, $value);
        }
    }
    public function testArrayIterator()
    {
        $var =new ArrayValue([['name'=>'aoeiuv'],['name'=>'DXkite'],['name'=>'TTHHR']]);
        foreach ($var  as $key => $value) {
            echo $value->name."\r\n";
        }
    }
}
