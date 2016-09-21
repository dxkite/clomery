<?php
namespace View;
/**
 * 
 */
class Includer  implements \Env_Method
{
    var $args=[];
    function __construct()
    {
       
    }
    function setParams(array $arguments)
    {
        $this->args=$arguments;
    }
    function render()
    {
        echo 'render Included Page';
    }
}
